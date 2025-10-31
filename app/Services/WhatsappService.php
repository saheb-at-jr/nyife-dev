<?php

namespace App\Services;

use App\Events\NewChatEvent;
use App\Helpers\WebhookHelper;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\ChatLog;
use App\Models\ChatMedia;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Template;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Models\User;
use App\Models\BalanceHistory;
use Illuminate\Support\Str;

class WhatsappService
{
    private $accessToken;
    private $apiVersion;
    private $appId;
    private $phoneNumberId;
    private $organizationId;
    private $wabaId;

    public function __construct($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $organizationId)
    {
        $this->accessToken = $accessToken;
        $this->apiVersion = $apiVersion;
        $this->appId = $appId;
        $this->phoneNumberId = $phoneNumberId;
        $this->wabaId = $wabaId;
        $this->organizationId = $organizationId;

        Log::info('Initializing WhatsappService', [
            'organization_id' => $organizationId,
            'api_version' => $apiVersion,
            'app_id' => $appId,
            'phone_number_id' => $phoneNumberId,
            'waba_id' => $wabaId,
        ]);

        $pusherKey = Setting::where('key', 'pusher_app_key')->first()->value ?? null;
        $pusherSecret = Setting::where('key', 'pusher_app_secret')->first()->value ?? null;
        $pusherAppId = Setting::where('key', 'pusher_app_id')->first()->value ?? null;
        $pusherCluster = Setting::where('key', 'pusher_app_cluster')->first()->value ?? null;

        Log::info('Configuring Pusher', [
            'key' => $pusherKey,
            'app_id' => $pusherAppId,
            'cluster' => $pusherCluster,
        ]);

        Config::set('broadcasting.connections.pusher', [
            'driver' => 'pusher',
            'key' => $pusherKey,
            'secret' => $pusherSecret,
            'app_id' => $pusherAppId,
            'options' => [
                'cluster' => $pusherCluster,
            ],
        ]);
    }


    /**
     * This function sends a text message via a POST request to the specified phone number using Facebook's messaging API.
     *
     * @param string $phoneNumber The phone number of the recipient.
     * @param string $messageContent The content of the message to be sent.
     * @return mixed Returns the response from the HTTP request.
     */
    public function sendMessage($contactUuId, $messageContent, $userId = null, $type = "text", $buttons = [], $header = [], $footer = null, $buttonLabel = null)
    {
        Log::info('Sending WhatsApp message started', [
            'contact_uuid' => $contactUuId,
            'type' => $type,
            'user_id' => $userId,
            'buttons' => $buttons,
            'header' => $header,
            'footer' => $footer,
            'button_label' => $buttonLabel,
        ]);

        $contact = Contact::where('uuid', $contactUuId)->first();
        if (!$contact) {
            Log::warning('Contact not found for sendMessage', ['contact_uuid' => $contactUuId]);
            return null;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $headers = $this->setHeaders();

        $requestData['messaging_product'] = 'whatsapp';
        $requestData['recipient_type'] = 'individual';
        $requestData['to'] = $contact->phone;

        if ($type === "text") {
            $requestData['type'] = 'text';
            $requestData['text']['preview_url'] = true;
            $requestData['text']['body'] = clean($messageContent);
            Log::info('Prepared text message payload', ['body' => $requestData['text']['body']]);
        } else {
            $requestData['type'] = 'interactive';
            $requestData['interactive']['body']['text'] = clean($messageContent);

            if ($type === "interactive buttons") {
                $requestData['interactive']['type'] = 'button';
                foreach ($buttons as $button) {
                    $requestData['interactive']['action']['buttons'][] = [
                        'type' => 'reply',
                        'reply' => [
                            'id' => $button['id'],
                            'title' => $button['title'],
                        ],
                    ];
                }
                Log::info('Prepared interactive buttons payload', ['buttons' => $requestData['interactive']['action']['buttons']]);
            } elseif ($type === "interactive call to action url") {
                $requestData['interactive']['type'] = 'cta_url';
                $requestData['interactive']['action']['name'] = "cta_url";
                $requestData['interactive']['action']['parameters'] = $buttons;
                Log::info('Prepared CTA URL payload', ['parameters' => $buttons]);
            } elseif ($type === "interactive list") {
                $requestData['interactive']['type'] = 'list';
                $requestData['interactive']['action']['sections'] = $buttons;
                $requestData['interactive']['action']['button'] = $buttonLabel;
                Log::info('Prepared interactive list payload', ['sections' => $buttons, 'button_label' => $buttonLabel]);
            }

            if (!empty($header)) {
                $requestData['interactive']['header'] = $header;
            }
            if ($footer != null) {
                $requestData['interactive']['footer'] = ['text' => clean($footer)];
            }
        }

        Log::info('Sending HTTP request to WhatsApp API', ['url' => $url, 'request_data' => $requestData]);

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        Log::info('WhatsApp API response received', ['response' => $responseObject]);

        if (!empty($responseObject->success) && $responseObject->success === true) {
            $response['text']['body'] = clean($messageContent);
            $response['type'] = 'text';

            $chat = Chat::create([
                'organization_id' => $contact->organization_id,
                'wam_id' => $responseObject->data->messages[0]->id,
                'contact_id' => $contact->id,
                'type' => 'outbound',
                'user_id' => $userId,
                'metadata' => json_encode($response),
                'status' => 'delivered',
            ]);
            Log::info('Chat record created', ['chat_id' => $chat->id]);

            $chat = Chat::with('contact', 'media')->where('id', $chat->id)->first();
            $responseObject->data->chat = $chat;

            $chatlogId = ChatLog::insertGetId([
                'contact_id' => $contact->id,
                'entity_type' => 'chat',
                'entity_id' => $chat->id,
                'created_at' => now()
            ]);
            Log::info('ChatLog created', ['chatlog_id' => $chatlogId]);

            $chatLogArray = ChatLog::where('id', $chatlogId)->where('deleted_at', null)->first();
            $chatArray = [['type' => 'chat', 'value' => $chatLogArray->relatedEntities]];

            event(new NewChatEvent($chatArray, $contact->organization_id));
            Log::info('NewChatEvent triggered', ['organization_id' => $contact->organization_id]);
        } else {
            Log::warning('WhatsApp message failed to send', ['response' => $responseObject]);
        }

        WebhookHelper::triggerWebhookEvent('message.sent', [
            'data' => $responseObject,
        ], $contact->organization_id);

        Log::info('Webhook triggered for message.sent', ['organization_id' => $contact->organization_id]);

        return $responseObject;
    }


    /**
     * This function sends a text message via a POST request to the specified phone number using Facebook's messaging API.
     *
     * @param string $phoneNumber The phone number of the recipient.
     * @param string $messageContent The content of the message to be sent.
     * @return mixed Returns the response from the HTTP request.
     */
    public function sendTemplateMessage($contactUuId, $templateContent, $userId = NULL, $campaignId = NULL, $mediaId = NULL)
    {
        Log::info('sendTemplateMessage: Started sending WhatsApp template message', [
            'contact_uuid' => $contactUuId,
            'campaign_id' => $campaignId,
            'media_id' => $mediaId,
            'user_id' => $userId,
            'template_content' => $templateContent,
        ]);

        $user = User::findOrFail($userId);

        if ($user->balance < 1) {
            Log::warning('Balance deduction skipped: insufficient balance', [
                'user_id' => $userId,
                'current_balance' => $user->balance,
            ]);
            return;
        }



        $campaign = Campaign::find($campaignId);

        if (!$campaign) {
            Log::warning('Campaign not found', ['campaign_id' => $campaignId]);
            return;
        }

        $metadata = json_decode($campaign->metadata, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Invalid campaign metadata JSON', [
                'campaign_id' => $campaignId,
                'error' => json_last_error_msg(),
            ]);
            return;
        }

        // Extract possible carousel structures
        $carousel = $metadata['carousel'] ?? [];

        if ($carousel) {
            $templateName = $templateContent['name'];
            $templateCustom = [
                "name" => $templateName,
                "language" => [
                    "code" => "en"
                ],
                "components" => [$carousel[0]]
            ];
            Log::info("----------------------------------------------");
            Log::info("templateCustom");
            Log::info(json_encode($templateCustom));
            Log::info("----------------------------------------------");

            $templateContent = [];
            $templateContent = $templateCustom;

        }



        Log::info('templateContent is here: ' . json_encode($templateContent));
        $contact = Contact::where('uuid', $contactUuId)->first();
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $headers = $this->setHeaders();

        $requestData['messaging_product'] = 'whatsapp';
        $requestData['recipient_type'] = 'individual';
        $requestData['to'] = $contact->phone;
        $requestData['type'] = 'template';
        $requestData['template'] = $templateContent;


        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success === true) {
            if ($campaignId != NULL) {
                $campaign = Campaign::where('id', $campaignId)->first();
                $templateMetadata = json_decode($campaign->metadata);
            }
            $previousBalance = $user->balance;
            $newBalance = $previousBalance - 0.84;

            $user->balance = $newBalance;
            $user->save();

            Log::info('Balance deducted successfully', [
                'user_id' => $userId,
                'previous_balance' => $previousBalance,
                'deducted_amount' => 0.84,
                'new_balance' => $newBalance,
            ]);

            $isCarousel = !empty($carousel);

            $chatMetadata = $campaignId != NULL
                ? $this->buildCampaignTemplateChatMessage($templateMetadata ?? null, $contactUuId)
                : $this->buildTemplateChatMessage($templateContent, $contact);

            // 🟡 Add body manually for carousel
            if ($isCarousel) {
                $chatMetadataArray = json_decode($chatMetadata, true);

                // Ensure metadata is valid JSON
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($chatMetadataArray)) {
                    $chatMetadataArray = [];
                }

                // Force add text body
                $chatMetadataArray['type'] = 'text';
                $chatMetadataArray['text'] = [
                    'body' => 'Your carousel message has been sent successfully.'
                ];

                $chatMetadata = json_encode($chatMetadataArray);
            }

            $chat = Chat::create([
                'organization_id' => $contact->organization_id,
                'wam_id' => $responseObject->data->messages[0]->id ?? 'manual_carousel_' . uniqid(),
                'contact_id' => $contact->id,
                'type' => 'outbound',
                'user_id' => $userId,
                'metadata' => $chatMetadata,
                'media_id' => $campaignId != NULL
                    ? $this->getMediaIdFromCampaign($campaignId)
                    : $mediaId,
                'status' => isset($responseObject->data->messages[0]->message_status)
                    ? $responseObject->data->messages[0]->message_status
                    : ($isCarousel ? 'sent' : 'unknown'),
                'created_at' => now()
            ]);

            Log::info("This is chat payload");
            Log::info($chat);




            $responseObject->data->chat = $chat;

            $chatlogId = ChatLog::insertGetId([
                'contact_id' => $contact->id,
                'entity_type' => 'chat',
                'entity_id' => $chat->id,
                'created_at' => now()
            ]);

            $chatLogArray = ChatLog::where('id', $chatlogId)->where('deleted_at', null)->first();
            $chatArray = array(
                [
                    'type' => 'chat',
                    'value' => $chatLogArray->relatedEntities
                ]
            );

            event(new NewChatEvent($chatArray, $contact->organization_id));
        }

        // Trigger webhook
        WebhookHelper::triggerWebhookEvent('message.sent', [
            'data' => $responseObject,
        ], $contact->organization_id);

        return $responseObject;
    }

    private function getMediaIdFromCampaign($campaignId)
    {
        Log::info('Fetching media ID from campaign', ['campaign_id' => $campaignId]);

        $campaign = Campaign::where('id', $campaignId)->first();

        if (!$campaign) {
            Log::warning('Campaign not found', ['campaign_id' => $campaignId]);
            return null;
        }

        $templateMetadata = json_decode($campaign->metadata);
        $mediaId = $templateMetadata->media ?? null;

        Log::info('Media ID retrieved from campaign', [
            'campaign_id' => $campaignId,
            'media_id' => $mediaId
        ]);

        return $mediaId;
    }

    private function buildCampaignTemplateChatMessage($templateMetadata, $contactUuId)
    {
        Log::info('Building campaign template chat message', [
            'contact_uuid' => $contactUuId,
            'template_metadata' => $templateMetadata
        ]);

        $contact = Contact::where('uuid', $contactUuId)->first();
        if (!$contact) {
            Log::warning('Contact not found in buildCampaignTemplateChatMessage', ['contact_uuid' => $contactUuId]);
            return json_encode([]);
        }

        $array = [];

        // Determine type
        $format = $templateMetadata->header->format ?? '';
        if (in_array($format, ['IMAGE', 'VIDEO', 'DOCUMENT', 'LOCATION'])) {
            $array['type'] = strtolower($format);
        } else {
            $array['type'] = 'text';
        }
        Log::info('Message type determined', ['type' => $array['type']]);

        // HEADER
        if (isset($templateMetadata->header->text)) {
            $headerText = $templateMetadata->header->text;

            if (!empty($templateMetadata->header->parameters)) {
                foreach ($templateMetadata->header->parameters as $index => $parameter) {
                    $placeholder = '{{' . ($index + 1) . '}}';
                    $value = $parameter->selection === 'static' ? $parameter->value : $this->getParameters($contact, $parameter->value);
                    $headerText = str_replace($placeholder, $value, $headerText);
                }
            }

            $array['header']['text'] = $headerText;
            Log::info('Header text processed', ['header_text' => $headerText]);
        }

        // BODY
        if (isset($templateMetadata->body->text)) {
            $bodyText = $templateMetadata->body->text;

            if (!empty($templateMetadata->body->parameters)) {
                foreach ($templateMetadata->body->parameters as $index => $parameter) {
                    $placeholder = '{{' . ($index + 1) . '}}';
                    $value = $parameter->selection === 'static' ? $parameter->value : $this->getParameters($contact, $parameter->value);
                    $bodyText = str_replace($placeholder, $value, $bodyText);
                }
            }

            if ($array['type'] == 'text') {
                $array[$array['type']]['body'] = $bodyText;
            } else {
                $array[$array['type']]['caption'] = $bodyText;
            }
            Log::info('Body text processed', ['body_text' => $bodyText]);
        }

        // FOOTER
        if (isset($templateMetadata->footer->text)) {
            $array[$array['type']]['footer'] = $templateMetadata->footer->text;
            Log::info('Footer text processed', ['footer_text' => $templateMetadata->footer->text]);
        }

        // BUTTONS
        if (isset($templateMetadata->buttons)) {
            foreach ($templateMetadata->buttons as $key => $button) {
                $array['buttons'][$key] = [
                    'type' => $button->type,
                    'text' => $button->text,
                    'value' => $button->value,
                ];

                if (isset($button->parameters)) {
                    $array['buttons'][$key]['parameters'] = $button->parameters;
                }
            }
            Log::info('Buttons processed', ['buttons' => $array['buttons'] ?? []]);
        }

        Log::info('Campaign template chat message built', ['chat_message' => $array]);

        return json_encode($array);
    }


    private function buildTemplateChatMessage($templateContent, $contact)
    {
        Log::info('Building template chat message started', [
            'contact_uuid' => $contact->uuid,
            'template_content' => $templateContent
        ]);

        // Get the template
        $template = Template::where('organization_id', $contact->organization_id)
            ->where('name', $templateContent['name'])
            ->where('language', $templateContent['language']['code'])
            ->first();

        if (!$template) {
            Log::warning('Template not found', [
                'organization_id' => $contact->organization_id,
                'template_name' => $templateContent['name'],
                'language_code' => $templateContent['language']['code']
            ]);
            return json_encode([]);
        }

        $template = json_decode($template->metadata);
        $templateMetadatas = $template->components ?? [];
        $array = [];
        $array['type'] = 'text';

        foreach ($templateMetadatas as $templateMetadata) {
            // HEADER
            if ($templateMetadata->type === 'HEADER') {
                if (in_array($templateMetadata->format, ['IMAGE', 'VIDEO', 'DOCUMENT', 'LOCATION'])) {
                    $array['type'] = strtolower($templateMetadata->format);
                } elseif ($templateMetadata->format === 'TEXT' && isset($templateMetadata->text)) {
                    $headerText = $templateMetadata->text;

                    if (!empty($templateContent['components'])) {
                        foreach ($templateContent['components'] as $component) {
                            if ($component['type'] === 'header' && !empty($component['parameters'])) {
                                foreach ($component['parameters'] as $index => $parameter) {
                                    $placeholder = '{{' . ($index + 1) . '}}';
                                    $value = $parameter['type'] === 'text' ? $parameter['text'] : $this->getParameters($contact, $parameter['text']);
                                    $headerText = str_replace($placeholder, $value, $headerText);
                                }
                                break;
                            }
                        }
                    }

                    $array['header']['text'] = $headerText;
                    Log::info('Header text processed', ['header_text' => $headerText]);
                }
            }

            // BODY
            if ($templateMetadata->type === 'BODY' && isset($templateMetadata->text)) {
                $bodyText = $templateMetadata->text;

                if (!empty($templateContent['components'])) {
                    foreach ($templateContent['components'] as $component) {
                        if ($component['type'] === 'body' && !empty($component['parameters'])) {
                            foreach ($component['parameters'] as $index => $parameter) {
                                $placeholder = '{{' . ($index + 1) . '}}';
                                $value = $parameter['type'] === 'text' ? $parameter['text'] : $this->getParameters($contact, $parameter['text']);
                                $bodyText = str_replace($placeholder, $value, $bodyText);
                            }
                            break;
                        }
                    }
                }

                if ($array['type'] === 'text') {
                    $array[$array['type']]['body'] = $bodyText;
                } else {
                    $array[$array['type']]['caption'] = $bodyText;
                }
                Log::info('Body text processed', ['body_text' => $bodyText]);
            }

            // FOOTER
            if ($templateMetadata->type === 'FOOTER') {
                $array[$array['type']]['footer'] = $templateMetadata->text;
                Log::info('Footer text processed', ['footer_text' => $templateMetadata->text]);
            }

            // BUTTONS
            if ($templateMetadata->type === 'BUTTONS') {
                foreach ($templateMetadata->buttons as $key => $button) {
                    $array['buttons'][$key] = [
                        'type' => $button->type,
                        'text' => $button->text,
                        'value' => $button->text,
                    ];

                    if (isset($button->parameters)) {
                        $array['buttons'][$key]['parameters'] = $button->parameters;
                    }
                }
                Log::info('Buttons processed', ['buttons' => $array['buttons']]);
            }
        }

        Log::info('Template chat message built', ['chat_message' => $array]);
        return json_encode($array);
    }


    private function getParameters($contact, $parameter)
    {
        $value = null;

        switch (strtolower($parameter)) {
            case 'first name':
                $value = $contact->first_name;
                break;
            case 'last name':
                $value = $contact->last_name;
                break;
            case 'name':
                $value = trim($contact->first_name . ' ' . $contact->last_name);
                break;
            case 'email':
                $value = $contact->email;
                break;
            case 'phone':
                $value = $contact->phone;
                break;
            default:
                $value = null;
                Log::warning('Unknown parameter in getParameters', [
                    'contact_uuid' => $contact->uuid ?? null,
                    'parameter' => $parameter
                ]);
                break;
        }

        Log::info('Parameter replaced', [
            'contact_uuid' => $contact->uuid ?? null,
            'parameter' => $parameter,
            'value' => $value
        ]);

        return $value;
    }


    /**
     * This function sends media content via a POST request and uploads the media to Facebook's resumable API.
     * Note that media types can only be audio, document, image, sticker, or video.
     *
     * @param string $phoneNumber The phone number of the recipient.
     * @param string $mediaType The type of media being uploaded. Valid options are audio, document, image, sticker, or video.
     * @param string $mediaFile The file to be uploaded as media.
     * @return mixed Returns the response from the HTTP request.
     */
    /*public function sendMedia($contactUuid, $mediaType, $mediaFile)
    {
        $contact = Contact::where('uuid', $contactUuId)->first();
        $mediaFilePath = Storage::path("media/{$mediaFileName}");

        $fileUploadResponse = $this->initiateResumableUploadSession($mediaFilePath);

        if(!$fileUploadResponse->success){
            return $fileUploadResponse;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $headers = $this->setHeaders();

        $requestData['messaging_product'] = 'whatsapp';
        $requestData['recipient_type'] = 'individual';
        $requestData['to'] = $contact->phone;
        $requestData['type'] = $mediaType;
        $requestData[$mediaType]['id'] = $fileUploadResponse->data->h;

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        dd($responseObject);
    }*/

    /**
     * This function sends a stored image as a media file via a POST request to the specified phone number using Facebook's messaging API.
     *
     * @param string $contactUuId The UUID of the contact to whom the image will be sent.
     * @param string $imageUrl The URL of the stored image.
     * @return mixed Returns the response from the HTTP request.
     */
    public function sendMedia($contactUuId, $mediaType, $mediaFileName, $mediaFilePath, $mediaUrl, $location, $caption = NULL, $transcription = NULL)
    {
        Log::info('sendMedia called', [
            'contact_uuid' => $contactUuId,
            'media_type' => $mediaType,
            'media_file_name' => $mediaFileName,
            'media_url' => $mediaUrl,
            'location' => $location,
            'caption' => $caption
        ]);

        $contact = Contact::where('uuid', $contactUuId)->first();
        if (!$contact) {
            Log::warning('Contact not found in sendMedia', ['contact_uuid' => $contactUuId]);
            return null;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $headers = $this->setHeaders();

        $requestData = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $contact->phone,
            'type' => $mediaType,
            $mediaType => [
                'link' => $mediaUrl
            ]
        ];

        if ($mediaType == 'document') {
            $requestData[$mediaType]['filename'] = $mediaFileName;
        }

        if ($caption != NULL && $mediaType != 'audio') {
            $requestData[$mediaType]['caption'] = $caption;
        }

        Log::info('Sending media request', ['request_data' => $requestData]);

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);
        Log::info('Media send HTTP response', ['response' => $responseObject]);

        if ($responseObject->success === true) {
            $wamId = $responseObject->data->messages[0]->id;
            $contentType = $this->getContentTypeFromUrl($mediaUrl);
            $mediaData = $this->formatMediaResponse($wamId, $mediaUrl, $mediaType, $contentType, $transcription);
            $mediaSize = $this->getMediaSizeInBytesFromUrl($mediaUrl);

            Log::info('Media data prepared', [
                'wam_id' => $wamId,
                'content_type' => $contentType,
                'size' => $mediaSize
            ]);

            $chat = Chat::create([
                'organization_id' => $contact->organization_id,
                'wam_id' => $wamId,
                'contact_id' => $contact->id,
                'type' => 'outbound',
                'metadata' => json_encode($mediaData),
                'status' => 'sent'
            ]);

            Log::info('Chat record created for media', ['chat_id' => $chat->id]);

            $chatlogId = ChatLog::insertGetId([
                'contact_id' => $contact->id,
                'entity_type' => 'chat',
                'entity_id' => $chat->id,
                'created_at' => now()
            ]);

            $media = ChatMedia::create([
                'name' => $mediaFileName,
                'path' => $mediaUrl,
                'location' => $location,
                'type' => $contentType,
                'size' => $mediaSize,
            ]);

            Chat::where('id', $chat->id)->update(['media_id' => $media->id]);
            Log::info('Media record linked to chat', ['chat_id' => $chat->id, 'media_id' => $media->id]);

            $chat = Chat::with('contact', 'media')->where('id', $chat->id)->first();
            $responseObject->data->chat = $chat;

            $chatLogArray = ChatLog::where('id', $chatlogId)->where('deleted_at', null)->first();
            $chatArray = [
                [
                    'type' => 'chat',
                    'value' => $chatLogArray->relatedEntities
                ]
            ];

            event(new NewChatEvent($chatArray, $contact->organization_id));
            Log::info('NewChatEvent triggered for media', ['contact_uuid' => $contactUuId]);
        } else {
            Log::warning('Failed to send media', [
                'contact_uuid' => $contactUuId,
                'media_url' => $mediaUrl,
                'response' => $responseObject
            ]);
        }

        // Trigger webhook
        WebhookHelper::triggerWebhookEvent('message.sent', [
            'data' => $responseObject,
        ], $contact->organization_id);

        return $responseObject;
    }

    private function getContentTypeFromUrl($url)
    {
        Log::info('Fetching content type from URL', ['url' => $url]);

        try {
            // Make a HEAD request to fetch headers only
            $response = Http::head($url);

            if ($response->successful()) {
                if ($response->hasHeader('Content-Type')) {
                    $contentType = $response->header('Content-Type');
                    Log::info('Content-Type fetched', [
                        'url' => $url,
                        'content_type' => $contentType
                    ]);
                    return $contentType;
                } else {
                    Log::warning('Content-Type header missing', ['url' => $url]);
                }
            } else {
                Log::warning('HEAD request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception fetching Content-Type', [
                'url' => $url,
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }


    private function formatMediaResponse($wamId, $mediaUrl, $mediaType, $contentType, $transcription = null)
    {
        $response = [
            "id" => $wamId,
            "type" => $mediaType,
            $mediaType => [
                "mime_type" => $contentType,
            ]
        ];

        if ($mediaType === 'audio' && $transcription) {
            $response['transcript'] = $transcription;
        }

        Log::info('Formatted media response', [
            'wam_id' => $wamId,
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
            'content_type' => $contentType,
            'transcription' => $transcription,
            'response_array' => $response
        ]);

        return $response;
    }


    private function getMediaSizeInBytesFromUrl($url)
    {
        Log::info('Fetching media size from URL', ['url' => $url]);

        try {
            $imageContent = file_get_contents($url);

            if ($imageContent !== false) {
                $size = strlen($imageContent);
                Log::info('Media size fetched', [
                    'url' => $url,
                    'size_bytes' => $size
                ]);
                return $size;
            } else {
                Log::warning('Failed to fetch media content', ['url' => $url]);
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching media size', [
                'url' => $url,
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
        }

        return null;
    }


    /**
     * This function allows you to react to a specific message with an emoji via a POST request to Facebook's messaging API.
     *
     * @param string $phoneNumber The phone number of the recipient.
     * @param string $wamId The ID of the message you want to react to.
     * @param string $emoji The emoji you want to use as a reaction.
     * @return mixed Returns the response from the HTTP request.
     */
    public function reactToMessage($phoneNumber, $wamId, $emoji)
    {
        Log::info('Reacting to WhatsApp message', [
            'phone_number' => $phoneNumber,
            'wam_id' => $wamId,
            'emoji' => $emoji
        ]);

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $headers = $this->setHeaders();

        $requestData = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'reaction',
            'reaction' => [
                'message_id' => $wamId,
                'emoji' => $emoji
            ]
        ];

        Log::info('WhatsApp API request data for reaction', ['request_data' => $requestData]);

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success ?? false) {
            Log::info('Reaction sent successfully', ['response' => $responseObject]);
        } else {
            Log::warning('Failed to send reaction', ['response' => $responseObject]);
        }

        return $responseObject;
    }

    /**
     * This function sends a location to a specific phone number via a POST request using Facebook's messaging API.
     *
     * @param string $phoneNumber The phone number of the recipient.
     * @param object $location The location object containing longitude, latitude, name, and address.
     * @return mixed Returns the response from the HTTP request.
     */
    public function sendLocation($phoneNumber, $location)
    {
        Log::info('Sending WhatsApp location', [
            'phone_number' => $phoneNumber,
            'location' => [
                'longitude' => $location->longitude,
                'latitude' => $location->latitude,
                'name' => $location->name,
                'address' => $location->address,
            ]
        ]);

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $headers = $this->setHeaders();

        $requestData = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'location',
            'location' => [
                'longitude' => $location->longitude,
                'latitude' => $location->latitude,
                'name' => $location->name,
                'address' => $location->address,
            ]
        ];

        Log::info('WhatsApp API request data for location', ['request_data' => $requestData]);

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success ?? false) {
            Log::info('Location sent successfully', ['response' => $responseObject]);
        } else {
            Log::warning('Failed to send location', ['response' => $responseObject]);
        }

        return $responseObject;
    }


    public function createTemplate(Request $request)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/message_templates";

        $requestData = [
            "name" => $request->name,
            "language" => $request->language,
            "category" => $request->category,
        ];

        if ($request->customize_ttl && $request->message_send_ttl_seconds) {
            $requestData['message_send_ttl_seconds'] = $request->message_send_ttl_seconds;
        }

        if ($request->category != 'AUTHENTICATION') {
            if ($request->header['format'] === 'TEXT') {
                if (isset($request->header['text'])) {
                    $headerComponent = [];

                    $headerComponent['type'] = "HEADER";
                    $headerComponent['format'] = $request->header['format'];
                    $headerComponent['text'] = $request->header['text'];

                    if (!empty($request->header['example'])) {
                        $headerComponent['example']['header_text'] = $request->header['example'];
                    }

                    $requestData['components'][] = $headerComponent;
                }
            }


            if (($request->header['format'] === 'IMAGE' || $request->header['format'] === 'VIDEO' || $request->header['format'] === 'DOCUMENT')) {
                if (isset($request->header['example'])) {
                    $fileUploadResponse = $this->initiateResumableUploadSession($request->header['example']);

                    if (!$fileUploadResponse->success) {
                        return $fileUploadResponse;
                    }

                    $requestData['components'][] = [
                        "type" => "HEADER",
                        "format" => $request->header['format'],
                        "example" => [
                            "header_handle" => [
                                $fileUploadResponse->data->h
                            ]
                        ]
                    ];
                }
            }
        }

        if ($request->category == 'AUTHENTICATION') {
            $bodyComponent = [];
            $bodyComponent['type'] = "BODY";
            $bodyComponent['add_security_recommendation'] = $request->body['add_security_recommendation'];

            $requestData['components'][] = $bodyComponent;
        } else {
            $bodyComponent = [];

            if ($request->body['text'] != null) {
                $bodyComponent['type'] = "BODY";
                $bodyComponent['text'] = $request->body['text'];

                if (!empty($request->body['example'])) {
                    $bodyComponent['example']['body_text'][] = $request->body['example'];
                }

                $requestData['components'][] = $bodyComponent;
            }
        }

        if ($request->has('footer')) {
            if ($request->category != 'AUTHENTICATION') {
                if (isset($request->footer['text']) && $request->footer['text'] != null) {
                    $requestData['components'][] = [
                        "type" => "FOOTER",
                        "text" => $request->footer['text']
                    ];
                }
            } else {
                $requestData['components'][] = [
                    "type" => "FOOTER",
                    "code_expiration_minutes" => $request->footer['code_expiration_minutes']
                ];
            }
        }

        if ($request->category != 'AUTHENTICATION') {
            if ($request->has('buttons')) {
                if (!isset($requestData['components'])) {
                    $requestData['components'] = [];
                }

                $requestData['components'][] = [
                    'type' => 'BUTTONS',
                    'buttons' => []
                ];

                $quickReplyButtons = [];

                foreach ($request->buttons as $button) {
                    if ($button['type'] === 'QUICK_REPLY') {
                        $quickReplyButtons[] = [
                            'type' => $button['type'],
                            'text' => $button['text'],
                        ];
                    }
                }

                foreach ($request->buttons as $button) {
                    if ($button['type'] !== 'QUICK_REPLY') {
                        if ($button['type'] === 'URL') {
                            $requestData['components'][count($requestData['components']) - 1]['buttons'][] = [
                                'type' => $button['type'],
                                'text' => $button['text'],
                                'url' => $button['url'],
                            ];
                        } elseif ($button['type'] === 'PHONE_NUMBER') {
                            $requestData['components'][count($requestData['components']) - 1]['buttons'][] = [
                                'type' => $button['type'],
                                'text' => $button['text'],
                                'phone_number' => $button['country'] . $button['phone_number'],
                            ];
                        } elseif ($button['type'] === 'COPY_CODE') {
                            $requestData['components'][count($requestData['components']) - 1]['buttons'][] = [
                                'type' => $button['type'],
                                'example' => $button['example'],
                            ];
                        }
                    }
                }

                // Add the quick reply buttons at the start
                if (!empty($quickReplyButtons)) {
                    $requestData['components'][count($requestData['components']) - 1]['buttons'] = array_merge($quickReplyButtons, $requestData['components'][count($requestData['components']) - 1]['buttons']);
                }
            }
        } else {
            $button = [
                'type' => $request->authentication_button['type'],
                'otp_type' => $request->authentication_button['otp_type'],
                'text' => $request->authentication_button['text'],
            ];

            if ($request->authentication_button['otp_type'] != 'copy_code') {
                $button['autofill_text'] = $request->authentication_button['autofill_text'];
                $button['supported_apps'] = $request->authentication_button['supported_apps'];
            }

            if ($request->authentication_button['otp_type'] === 'zero_tap') {
                $button['zero_tap_terms_accepted'] = $request->authentication_button['zero_tap_terms_accepted'];
            }

            $requestData['components'][] = [
                'type' => 'BUTTONS',
                'buttons' => [$button],
            ];
        }

        $client = new Client();
        $responseObject = new \stdClass();

        //\Log::info($requestData);

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            $responseObject->success = true;
            $responseObject->data = json_decode($response->getBody()->getContents());

            //Save Template To Database
            $template = new Template();
            $template->organization_id = session()->get('current_organization');
            $template->meta_id = $responseObject->data->id;
            $template->name = $request->name;
            $template->category = $request->category;
            $template->language = $request->language;
            $template->metadata = json_encode($requestData);
            $template->status = $responseObject->data->status;
            $template->created_by = auth()->user()->id;
            $template->created_at = now();
            $template->updated_at = now();
            $template->save();
        } catch (ConnectException $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->message = $e->getMessage();
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseObject->success = false;
            $responseObject->data = json_decode($response->getBody()->getContents());

            if (isset($responseObject->data->error->error_user_msg)) {
                $responseObject->message = $responseObject->data->error->error_user_msg;
            } else {
                $responseObject->message = $responseObject->data->error->message;
            }
        } catch (Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }


    public function updateTemplate(Request $request, $uuid)
    {
        $template = Template::where('uuid', $uuid)->first();
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$template->meta_id}";

        $requestData = [
            "category" => $template->status == 'APPROVED' ? $template->category : $request->category,
        ];

        if ($request->customize_ttl && $request->message_send_ttl_seconds) {
            $requestData['message_send_ttl_seconds'] = $request->message_send_ttl_seconds;
        }

        // Prepare header, body, footer, and buttons (existing logic) ...

        // Log request data before sending
        Log::info('WhatsApp Template Update Request', [
            'organization_id' => session()->get('current_organization'),
            'template_uuid' => $uuid,
            'request_data' => $requestData,
        ]);

        $client = new \GuzzleHttp\Client();
        $responseObject = new \stdClass();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            $responseBody = json_decode($response->getBody()->getContents());

            $responseObject->success = true;
            $responseObject->data = $responseBody;

            Log::info('WhatsApp Template Updated Successfully', [
                'template_uuid' => $uuid,
                'template_id' => $responseBody->id ?? null,
                'status' => $responseBody->status ?? null,
            ]);

            // Update Template in Database
            if ($template) {
                $template->organization_id = session()->get('current_organization');
                $template->category = $template->status == 'APPROVED' ? $template->category : $request->category;
                $template->status = 'PENDING';
                $template->created_by = auth()->user()->id;
                $template->updated_at = now();
                $template->save();
            } else {
                throw new \Exception('Template not found');
            }

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('WhatsApp Template Update Connection Error', [
                'template_uuid' => $uuid,
                'message' => $e->getMessage(),
                'organization_id' => session()->get('current_organization'),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->message = $e->getMessage();
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents());

            Log::error('WhatsApp Template Update API Error', [
                'template_uuid' => $uuid,
                'error' => $responseBody ?? $e->getMessage(),
                'organization_id' => session()->get('current_organization'),
            ]);

            $responseObject->success = false;
            $responseObject->data = $responseBody;
            $responseObject->message = $responseBody->error->error_user_msg ?? $responseBody->error->message ?? $e->getMessage();
        } catch (\Exception $e) {
            Log::error('Unexpected Error Updating WhatsApp Template', [
                'template_uuid' => $uuid,
                'message' => $e->getMessage(),
                'organization_id' => session()->get('current_organization'),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    function syncTemplates()
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/message_templates";

        $client = new Client();
        $responseObject = new \stdClass();

        try {
            do {
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Authorization' => "OAuth {$this->accessToken}",
                    ],
                ]);

                $responseObject = json_decode($response->getBody()->getContents());

                //dd($responseObject);

                foreach ($responseObject->data as $templateData) {
                    $template = Template::where('organization_id', session()->get('current_organization'))
                        ->where('meta_id', $templateData->id)->first();

                    if ($template) {
                        $template->metadata = json_encode($templateData);
                        $template->status = $templateData->status;
                        $template->updated_at = now();
                        $template->deleted_at = NULL;
                        $template->save();
                    } else {
                        $template = new Template();
                        $template->organization_id = session()->get('current_organization');
                        $template->meta_id = $templateData->id;
                        $template->name = $templateData->name;
                        $template->category = $templateData->category;
                        $template->language = $templateData->language;
                        $template->metadata = json_encode($templateData);
                        $template->status = $templateData->status;
                        $template->created_by = auth()->user()->id;
                        $template->created_at = now();
                        $template->updated_at = now();
                        $template->save();
                    }
                }
                ;

                if (isset($responseObject->paging) && isset($responseObject->paging->next)) {
                    $url = $responseObject->paging->next;
                } else {
                    $url = null; // Break the loop if no next page URL is available
                }
            } while ($url);
        } catch (ConnectException $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseObject->success = false;
            $responseObject->data = json_decode($response->getBody()->getContents());

            if (isset($responseObject->data->error->error_user_msg)) {
                $responseObject->message = $responseObject->data->error->error_user_msg;
            } else {
                $responseObject->message = $responseObject->data->error->message;
            }
        } catch (Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    /**
     * This function deletes a template by its UUID via a DELETE request to Facebook's messaging API.
     *
     * @param string $uuid The UUID of the template to be deleted.
     * @return mixed Returns the response from the HTTP request.
     */
    public function deleteTemplate($uuid)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/message_templates";
        $headers = $this->setHeaders();

        $template = Template::where('uuid', $uuid)->first();

        $requestData['hsm_id'] = $template->meta_id;
        $requestData['name'] = $template->name;

        $responseObject = $this->sendHttpRequest('DELETE', $url, $requestData, $headers);

        if ($responseObject->success) {
            $template->deleted_at = now();
            $template->save();
        }

        return $responseObject;
    }

    public function getMedia($mediaId)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$mediaId}";
        $headers = $this->setHeaders();

        Log::info('Fetching WhatsApp Media', [
            'media_id' => $mediaId,
            'url' => $url,
        ]);

        try {
            $responseObject = $this->sendHttpRequest('GET', $url, null, $headers);

            if ($responseObject->success === true) {
                Log::info('WhatsApp Media Retrieved Successfully', [
                    'media_id' => $mediaId,
                    'response' => $responseObject->data,
                ]);
            } else {
                Log::warning('WhatsApp Media Retrieval Failed', [
                    'media_id' => $mediaId,
                    'response' => $responseObject->data,
                ]);
            }

            return $responseObject;
        } catch (\Exception $e) {
            Log::error('Error fetching WhatsApp media', [
                'media_id' => $mediaId,
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            $responseObject = new \stdClass();
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = $e->getMessage();

            return $responseObject;
        }
    }

    public function uploadfile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // 100MB max
        ]);

        $file = $request->file('file');
        Log::info('Uploading file: ' . $file->getClientOriginalName());

        // Optional: store in temp/backup
        $path = $file->store('uploads', 'public');
        Log::info("Temp file stored at: storage/app/public/{$path}");

        try {
            Log::info("Sending file to WhatsApp Cloud API...");

            $response = \Illuminate\Support\Facades\Http::withToken($this->accessToken)
                ->attach(
                    'file',
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                )
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image', // You can make this dynamic
                ]);

            $responseData = $response->json();
            Log::info('WhatsApp API response:', $responseData);

            if ($response->successful() && isset($responseData['id'])) {
                $mediaId = $responseData['id'];
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$mediaId}.{$extension}";

                $storedPath = $file->storeAs("public", $fileName);
                Log::info("File saved locally as: storage/app/{$storedPath}");

                $publicUrl = asset("media/public/{$fileName}");
                Log::info("Public URL: {$publicUrl}");

                return response()->json([
                    'media_id' => $mediaId,
                    'public_url' => $publicUrl,
                    'file_name' => $file->getClientOriginalName(),
                ], 200);
            } else {
                Log::error("Failed to upload to WhatsApp", $responseData);
                return response()->json([
                    'error' => 'Failed to upload media to WhatsApp',
                    'details' => $responseData
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("Exception during upload: " . $e->getMessage());
            return response()->json([
                'error' => 'Exception occurred during upload',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    function checkHealth()
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}?fields=health_status";
        $headers = $this->setHeaders();

        $responseObject = $this->sendHttpRequest('GET', $url, NULL, $headers);

        return $responseObject;
    }

    function subscribeToWaba()
    {
        $responseObject = new \stdClass();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->post("https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/subscribed_apps")->throw()->json();

            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $response;
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    function getWabaSubscriptions()
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/subscribed_apps";
        $headers = $this->setHeaders();

        $responseObject = $this->sendHttpRequest('GET', $url, NULL, $headers);

        return $responseObject;
    }

    function overrideCallbackUrl($callbackUrl, $verifyToken)
    {
        $responseObject = new \stdClass();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->post("https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/subscribed_apps", [
                        'override_callback_uri' => $callbackUrl,
                        'verify_token' => $verifyToken
                    ])->throw()->json();

            $responseObject->success = true;
            $responseObject->data = new \stdClass();
            $responseObject->data = (object) $response;
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    function unSubscribeToWaba()
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/subscribed_apps";
        $headers = $this->setHeaders();

        $responseObject = $this->sendHttpRequest('DELETE', $url, NULL, $headers);

        return $responseObject;
    }

    public function getBusinessProfile()
    {
        $responseObject = new \stdClass();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/whatsapp_business_profile", [
                        'fields' => 'about,address,description,email,profile_picture_url,websites,vertical',
                    ])->throw()->json();

            if (isset($response['data']['error'])) {
                $responseObject->success = false;
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response['data']['error']['code'];
                $responseObject->data->error->message = $response['data']['error']['message'];
            } else {
                $responseObject->success = true;
                $responseObject->data = new \stdClass();
                $responseObject->data = (object) $response['data'][0];
            }
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    public function updateBusinessProfile(Request $request)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/whatsapp_business_profile";

        $headers = $this->setHeaders();

        $requestData['messaging_product'] = 'whatsapp';
        $requestData['about'] = $request->about;
        $requestData['address'] = $request->address;
        $requestData['description'] = $request->description;
        $requestData['vertical'] = $request->industry;
        $requestData['email'] = $request->email;

        $profile_picture_url = NULL;

        if ($request->hasFile('profile_picture_url')) {
            $storage = Setting::where('key', 'storage_system')->first()->value;
            $fileContent = $request->file('profile_picture_url');

            if ($storage === 'local') {
                $file = Storage::disk('local')->put('public', $fileContent);
                $mediaFilePath = $file;
                $profile_picture_url = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
            } else if ($storage === 'aws') {
                $file = $request->file('profile_picture_url');
                $uploadedFile = $file->store('uploads/media/sent/' . $this->organizationId, 's3');
                $mediaFilePath = Storage::disk('s3')->url($uploadedFile);
                $profile_picture_url = $mediaFilePath;
            }

            $fileUploadResponse = $this->initiateResumableUploadSession($request->file('profile_picture_url'));

            if ($fileUploadResponse->success) {
                $requestData['profile_picture_handle'] = $fileUploadResponse->data->h;
            }
        }

        $responseObject = $this->sendHttpRequest('POST', $url, $requestData, $headers);

        if ($responseObject->success === true) {
            $organizationConfig = Organization::where('id', $this->organizationId)->first();
            $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

            $metadataArray['whatsapp']['business_profile']['about'] = $request->about;
            $metadataArray['whatsapp']['business_profile']['address'] = $request->address;
            $metadataArray['whatsapp']['business_profile']['description'] = $request->description;
            $metadataArray['whatsapp']['business_profile']['industry'] = $request->industry;
            $metadataArray['whatsapp']['business_profile']['email'] = $request->email;
            if ($profile_picture_url != NULL) {
                $metadataArray['whatsapp']['business_profile']['profile_picture_url'] = $profile_picture_url;
            }

            $updatedMetadataJson = json_encode($metadataArray);

            $organizationConfig->metadata = $updatedMetadataJson;
            $organizationConfig->save();
        }

        return $responseObject;
    }

    public function deRegisterPhone()
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/deregister";

        $headers = $this->setHeaders();

        $responseObject = $this->sendHttpRequest('POST', $url, NULL, $headers);

        if ($responseObject->success === true) {
            dd($responseObject);
        }

        dd($responseObject);
        return $responseObject;
    }

    public function getPhoneNumberId()
    {
        $responseObject = new \stdClass();

        try {
            $fields = 'display_phone_number,certificate,name_status,new_certificate,new_name_status,verified_name,quality_rating,messaging_limit_tier';

            $response = Http::get("https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}/phone_numbers", [
                'fields' => $fields,
                'access_token' => $this->accessToken,
            ])->throw()->json();

            if (isset($response['data']['error'])) {
                $responseObject->success = false;
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response['data']['error']['code'];
                $responseObject->data->error->message = $response['data']['error']['message'];
            } else {
                $responseObject->success = true;
                $responseObject->data = new \stdClass();
                $responseObject->data = (object) $response['data'][0];
            }
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    public function getPhoneNumberStatus()
    {
        $responseObject = new \stdClass();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}", [
                        'fields' => 'status, code_verification_status , quality_score, health_status',
                    ])->throw()->json();

            if (isset($response['data']['error'])) {
                $responseObject->success = false;
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response['data']['error']['code'];
                $responseObject->data->error->message = $response['data']['error']['message'];
            } else {
                $responseObject->success = true;
                $responseObject->data = new \stdClass();
                $responseObject->data = (object) $response;
            }
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    public function getAccountReviewStatus()
    {
        $responseObject = new \stdClass();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get("https://graph.facebook.com/{$this->apiVersion}/{$this->wabaId}", [
                        'fields' => 'account_review_status',
                    ])->throw()->json();

            if (isset($response['data']['error'])) {
                $responseObject->success = false;
                $responseObject->data = new \stdClass();
                $responseObject->data->error = new \stdClass();
                $responseObject->data->error->code = $response['data']['error']['code'];
                $responseObject->data->error->message = $response['data']['error']['message'];
            } else {
                $responseObject->success = true;
                $responseObject->data = new \stdClass();
                $responseObject->data = (object) $response;
            }
        } catch (\Exception $e) {
            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    function viewMedia($mediaId)
    {
        $response = $this->getMedia($mediaId);

        if (!$response->success) {
            return $response;
        }

        $url = $response->data->url;
        $headers = $this->setHeaders();

        $responseObject = $this->sendHttpRequest('GET', $url, NULL, $headers);

        dd($responseObject);

        return $responseObject;
    }

    function initiateResumableUploadSession($file)
    {
        Log::info('Initiating resumable upload session', [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        $sessionResponse = $this->createResumableUploadSession($file);

        if (!$sessionResponse->success) {
            Log::error('Failed to create upload session', [
                'file_name' => $file->getClientOriginalName(),
                'response' => $sessionResponse,
            ]);
            return $sessionResponse;
        }

        $uploadSessionId = $sessionResponse->data->id;
        $fileName = $file->getPathname();
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$uploadSessionId}";

        $client = new Client();
        $responseObject = new \stdClass();

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => "OAuth {$this->accessToken}",
                    'file_offset' => 0,
                ],
                'body' => fopen($fileName, 'r'),
                'timeout' => 2,
            ]);

            $responseObject->success = true;
            $responseObject->data = json_decode($response->getBody()->getContents());

            Log::info('Resumable upload session initiated successfully', [
                'upload_session_id' => $uploadSessionId,
                'file_name' => $file->getClientOriginalName(),
                'response' => $responseObject->data,
            ]);
        } catch (ConnectException $e) {
            Log::error('Connection error during resumable upload session', [
                'file_name' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseObject->success = false;
            $responseObject->data = json_decode($response->getBody()->getContents());

            Log::error('Guzzle exception during resumable upload session', [
                'file_name' => $file->getClientOriginalName(),
                'response' => $responseObject->data,
            ]);

            $responseObject->message = $responseObject->data->error->error_user_msg ?? $responseObject->data->error->message;
        } catch (Exception $e) {
            Log::error('General exception during resumable upload session', [
                'file_name' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }

    function createResumableUploadSession($file)
    {
        $fileLength = $file->getSize();
        $fileType = $file->getMimeType();
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->appId}/uploads";

        Log::info('Creating resumable upload session', [
            'file_name' => $file->getClientOriginalName(),
            'file_length' => $fileLength,
            'file_type' => $fileType,
            'url' => $url,
        ]);

        $client = new Client();
        $responseObject = new \stdClass();

        try {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'file_length' => $fileLength,
                    'file_type' => $fileType,
                    'access_token' => $this->accessToken,
                ]
            ]);

            $responseObject->success = true;
            $responseObject->data = json_decode($response->getBody()->getContents());

            Log::info('Upload session created successfully', [
                'file_name' => $file->getClientOriginalName(),
                'response' => $responseObject->data,
            ]);
        } catch (ConnectException $e) {
            Log::error('Connection error creating upload session', [
                'file_name' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseObject->success = false;
            $responseObject->data = json_decode($response->getBody()->getContents());

            Log::error('Guzzle exception creating upload session', [
                'file_name' => $file->getClientOriginalName(),
                'response' => $responseObject->data,
            ]);

            $responseObject->message = $responseObject->data->error->error_user_msg ?? $responseObject->data->error->message;
        } catch (Exception $e) {
            Log::error('General exception creating upload session', [
                'file_name' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }


    //Set the headers for request
    public function setHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];
    }

    // Private method to send an HTTP request
    private function sendHttpRequest($method, $url, $data = [], $headers = [])
    {
        $client = new Client();
        $responseObject = new \stdClass();

        // Log the request
        Log::info('Sending HTTP request', [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'data' => $data,
        ]);

        try {
            $requestOptions = [
                'headers' => $headers,
            ];

            if (!empty($data) && in_array($method, ['POST', 'PUT', 'DELETE'])) {
                $requestOptions['json'] = $data;
            }

            $response = $client->request($method, $url, $requestOptions);
            $responseObject->success = true;
            $responseObject->data = json_decode($response->getBody()->getContents());

            // Log the successful response
            Log::info('HTTP request successful', [
                'method' => $method,
                'url' => $url,
                'response' => $responseObject->data,
            ]);
        } catch (ConnectException $e) {
            Log::error('Connection error during HTTP request', [
                'method' => $method,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseObject->success = false;
            $responseObject->data = json_decode($response->getBody()->getContents());

            Log::error('Guzzle exception during HTTP request', [
                'method' => $method,
                'url' => $url,
                'response' => $responseObject->data,
            ]);

            $responseObject->message = $responseObject->data->error->error_user_msg ?? $responseObject->data->error->message;
        } catch (Exception $e) {
            Log::error('General exception during HTTP request', [
                'method' => $method,
                'url' => $url,
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            $responseObject->success = false;
            $responseObject->data = new \stdClass();
            $responseObject->data->error = new \stdClass();
            $responseObject->data->error->message = $e->getMessage();
        }

        return $responseObject;
    }



    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png|max:5120', // max 5MB
        ]);

        $file = $request->file('image');
        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        $tempPath = $file->storeAs('temp', $fileName); // stored in storage/app/temp
        $filePath = storage_path('app/' . $tempPath);

        $appId = $this->appId;
        $accessToken = $this->accessToken;

        // Step 1: Start upload session
        $sessionResponse = Http::post("https://graph.facebook.com/{$this->apiVersion}/{$appId}/uploads", [
            'file_name' => $fileName,
            'file_length' => $fileSize,
            'file_type' => $mimeType,
            'access_token' => $accessToken,
        ]);

        if (!$sessionResponse->successful()) {
            return response()->json(['error' => 'Upload session failed', 'details' => $sessionResponse->json()], 500);
        }

        $uploadSessionId = $sessionResponse->json('id'); // e.g. upload:xxxxx

        // Step 2: Upload the binary file
        $uploadUrl = "https://graph.facebook.com/v23.0/{$uploadSessionId}";
        $fileBinary = file_get_contents($filePath);

        $uploadResponse = Http::withHeaders([
            'Authorization' => "OAuth {$accessToken}",
            'file_offset' => 0,
        ])->withBody($fileBinary, 'application/octet-stream')->post($uploadUrl);

        // Clean up
        Storage::delete($tempPath);

        if (!$uploadResponse->successful()) {
            return response()->json(['error' => 'File upload failed', 'details' => $uploadResponse->json()], 500);
        }

        return response()->json([
            'file_handle' => $uploadResponse->json('h'),
        ]);
    }


    public function createCrousel(Request $request)
    {
        Log::info('Request JSON in Whatsapp Service: ' . json_encode($request->all()));

        $requestData = $request->all();

        // ✅ Remove "example" if body_text is empty
        foreach ($requestData['components'] as &$component) {
            if (
                $component['type'] === 'body' &&
                isset($component['example']['body_text']) &&
                empty($component['example']['body_text'][0])
            ) {
                unset($component['example']); // remove the whole example object
            }
        }

        // Guzzle client
        $client = new Client();

        $appId = $this->wabaId;
        $accessToken = $this->accessToken; // or env('META_ACCESS_TOKEN')
        Log::info('Meta Access Token: ' . $accessToken);
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$appId}/message_templates";
        Log::info('Meta API URL: ' . $url);

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('✅ Meta API Success:', $responseBody);

            return response()->json([
                'success' => true,
                'data' => $responseBody,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            Log::error('❌ Meta API ClientException:', $errorBody);

            return response()->json([
                'success' => false,
                'error' => $errorBody,
            ], $e->getCode());
        } catch (\Exception $e) {
            Log::error('❌ General Exception in createCrousel:', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unexpected server error.',
            ], 500);
        }
    }





}