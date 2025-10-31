<?php

namespace App\Services;

use App\Helpers\WebhookHelper;
use App\Http\Resources\AutoReplyResource;
use App\Models\AutoReply;
use App\Models\Chat;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Setting;
use App\Services\MediaService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use DB;
use Validator;

class AutoReplyService
{
    public function getRows(object $request)
    {
        Log::info('Fetching AutoReply rows', ['request' => $request->all()]);
        $organizationId = session()->get('current_organization');
        $model = new AutoReply;
        $searchTerm = $request->query('search');

        $result = AutoReplyResource::collection($model->listAll($organizationId, $searchTerm));
        Log::info('Fetched AutoReply rows', ['count' => $result->count()]);
        return $result;
    }

    public function store(object $request, $uuid = null)
    {
        Log::info('Storing AutoReply', ['uuid' => $uuid, 'request' => $request->all()]);
        $model = $uuid == null ? new AutoReply : AutoReply::where('uuid', $uuid)->first();
        $model['name'] = $request->name;
        $model['trigger'] = $request->trigger;
        $model['match_criteria'] = $request->match_criteria;

        $metadata['type'] = $request->response_type;
        if($request->response_type === 'image' || $request->response_type === 'audio'){
            if($request->hasFile('response')){
                Log::info('Uploading media response');
                $storage = Setting::where('key', 'storage_system')->first()->value;
                $fileName = $request->file('response')->getClientOriginalName();
                $fileContent = $request->file('response');

                if($storage === 'local'){
                    $file = Storage::disk('local')->put('public', $fileContent);
                    $mediaFilePath = $file;
                    $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
                    Log::info('Stored media locally', ['path' => $mediaFilePath, 'url' => $mediaUrl]);
                } else if($storage === 'aws') {
                    $filePath = 'uploads/media/received'  . session()->get('current_organization') . '/' . $fileName;
                    $file = Storage::disk('s3')->put($filePath, $fileContent, 'public');
                    $mediaFilePath = Storage::disk('s3')->url($filePath);
                    $mediaUrl = $mediaFilePath;
                    Log::info('Stored media on AWS S3', ['path' => $filePath, 'url' => $mediaUrl]);
                }

                $uploadedMedia = MediaService::upload($request->file('response'));
                Log::info('Uploaded media to MediaService', ['uploadedMedia' => $uploadedMedia]);

                $metadata['data']['file']['name'] = $fileName;
                $metadata['data']['file']['location'] = $mediaFilePath;
                $metadata['data']['file']['url'] = $mediaUrl;
            } else {
                Log::info('No new file uploaded, using existing metadata');
                $media = json_decode($model->metadata)->data;
                $metadata['data']['file']['name'] = $media->file->name;
                $metadata['data']['file']['location'] = $media->file->location;
                $metadata['data']['file']['url'] = $media->file->url;
            }
        } else if($request->response_type === 'text') {
            $metadata['data']['text'] = $request->response;
            Log::info('Storing text response', ['text' => $request->response]);
        } else {
            $metadata['data']['template'] = $request->response;
            Log::info('Storing template response', ['template' => $request->response]);
        }

        $model['metadata'] = json_encode($metadata);
        $model['updated_at'] = now();

        if($uuid === null){
            $model['organization_id'] = session()->get('current_organization');
            $model['created_by'] = auth()->user()->id;
            $model['created_at'] = now();
            Log::info('Creating new AutoReply');
        } else {
            Log::info('Updating existing AutoReply', ['uuid' => $uuid]);
        }

        $model->save();
        Log::info('AutoReply saved successfully', ['id' => $model->id]);

        // Prepare a clean contact object for webhook
        $cleanModel = $model->makeHidden(['id', 'organization_id', 'created_by']);

        // Trigger webhook
        WebhookHelper::triggerWebhookEvent($uuid === null ? 'autoreply.created' : 'autoreply.updated', $cleanModel);
        Log::info('Webhook triggered for AutoReply', ['event' => $uuid === null ? 'autoreply.created' : 'autoreply.updated']);
    }

    public function destroy($uuid)
    {
        Log::info('Deleting AutoReply', ['uuid' => $uuid]);
        AutoReply::where('uuid', $uuid)->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now()
        ]);

        Log::info('AutoReply deleted successfully', ['uuid' => $uuid]);

        // Trigger webhook
        WebhookHelper::triggerWebhookEvent('autoreply.deleted', [
            'list' => [
                'uuid' => $uuid,
                'deleted_at' => now()->toISOString()
            ],
        ]);
        Log::info('Webhook triggered for AutoReply deletion', ['uuid' => $uuid]);
    }

    public function checkAutoReply(Chat $chat, $isNewContact)
    {
        Log::info('Checking AutoReply', ['chat_id' => $chat->id, 'isNewContact' => $isNewContact]);
        $organizationId = $chat->organization_id;

        return $this->replySequence($organizationId, $chat, $isNewContact);
    }

    private function replySequence($organizationId, $chat, $isNewContact)
    {
        Log::info('Running reply sequence', ['organizationId' => $organizationId, 'chat_id' => $chat->id]);
        $organizationConfig = Organization::where('id', $organizationId)->first();
        $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];
        $activeFlow = false;
        $modulePath = base_path('modules/FlowBuilder');
        
        if (File::exists($modulePath)) {
            if (class_exists(\Modules\FlowBuilder\Services\FlowExecutionService::class)) {
                $query = new \Modules\FlowBuilder\Services\FlowExecutionService($organizationId);
                $activeFlow = $query->hasActiveFlow($chat);
                Log::info('Checked active flow', ['activeFlow' => $activeFlow]);
            }
        }

        // Override response sequence if there is an active flow
        if ($activeFlow) {
            $response_sequence = ['Automated Flows'];
        } else {
            $response_sequence = $metadataArray['automation']['response_sequence'] ?? ['Basic Replies', 'Automated Flows', 'AI Reply Assistant'];
        }
        Log::info('Response sequence determined', ['sequence' => $response_sequence]);

        $sequenceFunctions = [
            'Basic Replies' => function() use ($chat) {
                return $this->handleBasicReplies($chat);
            },
            'Automated Flows' => function() use ($organizationId, $chat, $isNewContact) {
                return $this->handleAutomatedFlows($organizationId, $chat, $isNewContact);
            },
            'AI Reply Assistant' => function() use ($chat) {
                return $this->handleAIReplyAssistant($chat);
            },
        ];

        $response = null;

        foreach ($response_sequence as $sequenceItem) {
            if (isset($sequenceFunctions[$sequenceItem])) {
                Log::info('Executing sequence item', ['item' => $sequenceItem]);
                $response = $sequenceFunctions[$sequenceItem]();
                Log::info('Sequence item response', ['item' => $sequenceItem, 'response' => $response]);

                if ($response) {
                    Log::info('Response found, stopping sequence', ['item' => $sequenceItem]);
                    break;
                }
            }
        }

        return $response;
    }

    private function handleBasicReplies($chat)
    {
        Log::info('Handling Basic Replies', ['chat_id' => $chat->id]);
        $organizationId = $chat->organization_id;
        $text = '';
        $metadata = json_decode($chat->metadata, true);

        if($metadata['type'] == 'text'){
            $text = $metadata['text']['body'];
        } else if(json_decode($chat->metadata)->type == 'button'){
            $text = $metadata['button']['payload'];
        } else if(json_decode($chat->metadata)->type == 'interactive'){
            if($metadata['interactive']['type'] == 'button_reply'){
                $text = $metadata['interactive']['button_reply']['title'];
            } else if($metadata['interactive']['type'] == 'list_reply'){
                $text = $metadata['interactive']['list_reply']['title'];
            }
        }
        
        $receivedMessage = " " . $text;
        Log::info('Received message for basic replies', ['message' => $receivedMessage]);

        $autoReplies = AutoReply::where('organization_id', $organizationId)
            ->where('deleted_at', null)
            ->get();
        Log::info('Fetched AutoReplies for organization', ['count' => $autoReplies->count()]);

        foreach ($autoReplies as $autoReply) {
            $triggerValues = $this->getTriggerValues($autoReply->trigger);
            Log::info('Checking triggers', ['autoReplyId' => $autoReply->id, 'triggers' => $triggerValues]);

            foreach ($triggerValues as $trigger) {
                if ($this->checkMatch($receivedMessage, $trigger, $autoReply->match_criteria)) {
                    Log::info('Match found, sending reply', ['trigger' => $trigger, 'criteria' => $autoReply->match_criteria]);
                    $this->sendReply($chat, $autoReply);
                    return true;
                }
            }
        }

        Log::info('No match found in Basic Replies');
        return false;
    }

    private function handleAIReplyAssistant($chat)
    {
        Log::info('Handling AI Reply Assistant', ['chat_id' => $chat->id]);
        $text = '';
        $metadata = json_decode($chat->metadata, true);

        if($metadata['type'] == 'text'){
            $text = $metadata['text']['body'];
        } else if(json_decode($chat->metadata)->type == 'button'){
            $text = $metadata['button']['payload'];
        } else if(json_decode($chat->metadata)->type == 'interactive'){
            if($metadata['interactive']['type'] == 'button_reply'){
                $text = $metadata['interactive']['button_reply']['title'];
            } else if($metadata['interactive']['type'] == 'list_reply'){
                $text = $metadata['interactive']['list_reply']['title'];
            }
        }
        
        $receivedMessage = " " . $text;
        Log::info('Received message for AI assistant', ['message' => $receivedMessage]);

        if (file_exists(base_path('modules/IntelliReply/Services/AIResponseService.php'))) {
            $query = new \Modules\IntelliReply\Services\AIResponseService();
            if ($query->handleAIResponse($chat, $receivedMessage)) {
                Log::info('AI assistant generated response');
                return true;
            }
        }

        Log::info('No AI assistant response generated');
        return false;
    }

    private function handleAutomatedFlows($organizationId, $chat, $isNewContact)
    {
        Log::info('Handling Automated Flows', ['chat_id' => $chat->id, 'isNewContact' => $isNewContact]);
        $text = '';
        $metadata = json_decode($chat->metadata, true);

        if($metadata['type'] == 'text'){
            $text = $metadata['text']['body'];
        } else if(json_decode($chat->metadata)->type == 'button'){
            $text = $metadata['button']['payload'];
        } else if(json_decode($chat->metadata)->type == 'interactive'){
            if($metadata['interactive']['type'] == 'button_reply'){
                $text = $metadata['interactive']['button_reply']['title'];
            } else if($metadata['interactive']['type'] == 'list_reply'){
                $text = $metadata['interactive']['list_reply']['title'];
            }
        }

        $receivedMessage = " " . $text;
        Log::info('Received message for automated flows', ['message' => $receivedMessage]);

        if (file_exists(base_path('modules/FlowBuilder/Services/FlowExecutionService.php'))) {
            $query = new \Modules\FlowBuilder\Services\FlowExecutionService($organizationId);
            $result = $query->executeFlow($chat, $isNewContact, $receivedMessage);
            Log::info('Executed automated flow', ['result' => $result]);
            return $result;
        }

        Log::info('No automated flow service found');
        return false;
    }

    private function getTriggerValues($trigger)
    {
        return is_string($trigger) && strpos($trigger, ',') !== false
            ? explode(',', $trigger)
            : (array) $trigger;
    }

    private function checkMatch($receivedMessage, $trigger, $criteria)
    {
        Log::info('Checking match', ['message' => $receivedMessage, 'trigger' => $trigger, 'criteria' => $criteria]);
        $normalizedTrigger = trim($trigger);

        if ($criteria === 'exact match') {
            $hasArabic = preg_match('/[\p{Arabic}]/u', $receivedMessage . $normalizedTrigger);
            
            if ($hasArabic) {
                return $receivedMessage === " " . $normalizedTrigger;
            } else {
                return strtolower($receivedMessage) === " " . strtolower($normalizedTrigger);
            }
        } else if ($criteria === 'contains') {
            $triggerWords = explode(' ', $normalizedTrigger);
            $hasArabic = preg_match('/[\p{Arabic}]/u', $receivedMessage . $normalizedTrigger);
            
            if ($hasArabic) {
                foreach ($triggerWords as $word) {
                    if (strpos($receivedMessage, $word) !== false) {
                        return true;
                    }
                }
                return false;
            } else {
                $pattern = '/\b(' . implode('|', array_map('preg_quote', $triggerWords)) . ')\b/i';
                return preg_match($pattern, strtolower($receivedMessage)) === 1;
            }
        }
    
        return false;
    }

    protected function sendReply(Chat $chat, AutoReply $autoreply)
    {
        Log::info('Sending reply', ['chat_id' => $chat->id, 'autoreply_id' => $autoreply->id]);
        $contact = Contact::where('id', $chat->contact_id)->first();
        $organization_id = $chat->organization_id;
        $metadata = json_decode($autoreply->metadata);
        $replyType = $metadata->type;

        if($replyType === 'text'){
            $message = $this->replacePlaceholders($organization_id, $contact->uuid, $metadata->data->text);
            Log::info('Sending text reply', ['message' => $message]);
            $this->initializeWhatsappService($organization_id)->sendMessage($contact->uuid, $message);
        } else if($replyType === 'audio' || $replyType === 'image'){
            $location = strpos($metadata->data->file->location, 'public\\') === 0 ? 'local' : 'amazon';
            Log::info('Sending media reply', ['type' => $replyType, 'file' => $metadata->data->file]);
            $this->initializeWhatsappService($organization_id)->sendMedia($contact->uuid, $replyType, $metadata->data->file->name, $metadata->data->file->location, $metadata->data->file->url, $location);
        }
    }

    private function initializeWhatsappService($organizationId)
    {
        Log::info('Initializing WhatsappService', ['organizationId' => $organizationId]);
        $config = Organization::where('id', $organizationId)->first()->metadata;
        $config = $config ? json_decode($config, true) : [];

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version');
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        return new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $organizationId);
    }

    private function replacePlaceholders($organizationId, $contactUuid, $message){
        Log::info('Replacing placeholders in message', ['contactUuid' => $contactUuid]);
        $organization = Organization::where('id', $organizationId)->first();
        $contact = Contact::with('contactGroups')->where('uuid', $contactUuid)->first();
        $address = $contact->address ? json_decode($contact->address, true) : [];
        $metadata = $contact->metadata ? json_decode($contact->metadata, true) : [];
        $full_address = ($address['street'] ?? Null) . ', ' .
                        ($address['city'] ?? Null) . ', ' .
                        ($address['state'] ?? Null) . ', ' .
                        ($address['zip'] ?? Null) . ', ' .
                        ($address['country'] ?? Null);

        $data = [
            'first_name' => $contact->first_name ?? Null,
            'last_name' => $contact->last_name ?? Null,
            'full_name' => $contact->full_name ?? Null,
            'email' => $contact->email ?? Null,
            'phone' => $contact->phone ?? Null,
            'organization_name' => $organization->name,
            'full_address' => $full_address,
            'street' => $address['street'] ?? Null,
            'city' => $address['city'] ?? Null,
            'state' => $address['state'] ?? Null,
            'zip_code' => $address['zip'] ?? Null,
            'country' => $address['country'] ?? Null,
        ];

        $transformedMetadata = [];
        if($metadata){
            foreach ($metadata as $key => $value) {
                $transformedKey = strtolower(str_replace(' ', '_', $key));
                $transformedMetadata[$transformedKey] = $value;
            }
        }

        $mergedData = array_merge($data, $transformedMetadata);
        Log::info('Prepared data for placeholder replacement', ['data' => $mergedData]);

        $message = preg_replace_callback('/\{url:(\w+)\}/', function ($matches) use ($mergedData) {
            $key = $matches[1];
            if (isset($mergedData[$key])) {
                return rawurlencode($mergedData[$key]);
            }
            return $matches[0];
        }, $message);
        
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($mergedData) {
            $key = $matches[1];
            return isset($mergedData[$key]) ? $mergedData[$key] : $matches[0];
        }, $message);
    }
}
