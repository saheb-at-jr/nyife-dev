<?php

namespace App\Services;

use Carbon\Carbon;
use App\Jobs\SendCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\ChatMedia;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Template;
use App\Services\WhatsappService;
use App\Traits\TemplateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Validator;
use Illuminate\Http\Request;

class CampaignService
{
    use TemplateTrait;

    public function store(object $request)
    {
        $organizationId = session()->get('current_organization');
        Log::info('Storing campaign started', [
            'organization_id' => $organizationId,
            'template_uuid' => $request->template,
            'contacts' => $request->contacts,
            'request' => $request->all()
        ]);

        $timezone = Setting::where('key', 'timezone')->value('value');
        $organization = Organization::find($organizationId);
        $organizationMetadata = json_decode($organization->metadata ?? '{}', true);
        $timezone = $organizationMetadata['timezone'] ?? $timezone;

        $template = Template::where('uuid', $request->template)->first();
        $contactGroup = ContactGroup::where('uuid', $request->contacts)->first();

        try {
            DB::transaction(function () use ($request, $organizationId, $template, $contactGroup, $timezone) {
                Log::info('Inside DB transaction for campaign creation');
                $mediaId = null;
                if (in_array($request->header['format'], ['IMAGE', 'DOCUMENT', 'VIDEO'])) {
                    Log::info('Campaign has media header', ['format' => $request->header['format']]);
                    $header = $request->header;

                    if ($request->header['parameters']) {
                        $metadata['header']['format'] = $header['format'];
                        $metadata['header']['parameters'] = [];

                        foreach ($request->header['parameters'] as $key => $parameter) {
                            Log::info('Processing campaign header parameter', ['key' => $key, 'selection' => $parameter['selection']]);
                            if ($parameter['selection'] === 'upload') {
                                $storage = Setting::where('key', 'storage_system')->first()->value;
                                $fileName = $parameter['value']->getClientOriginalName();
                                $fileContent = $parameter['value'];

                                if ($storage === 'local') {
                                    $file = Storage::disk('local')->put('public', $fileContent);
                                    $mediaFilePath = $file;
                                    $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
                                    Log::info('Stored campaign media locally', ['file' => $mediaFilePath]);
                                } else if ($storage === 'aws') {
                                    $file = $parameter['value'];
                                    $uploadedFile = $file->store('uploads/media/sent/' . $organizationId, 's3');
                                    $mediaFilePath = Storage::disk('s3')->url($uploadedFile);
                                    $mediaUrl = $mediaFilePath;
                                    Log::info('Stored campaign media on S3', ['file' => $uploadedFile]);
                                }

                                $contentType = $this->getContentTypeFromUrl($mediaUrl);
                                $mediaSize = $this->getMediaSizeInBytesFromUrl($mediaUrl);

                                $chatMedia = new ChatMedia;
                                $chatMedia->name = $fileName;
                                $chatMedia->path = $mediaUrl;
                                $chatMedia->type = $contentType;
                                $chatMedia->size = $mediaSize;
                                $chatMedia->created_at = now();
                                $chatMedia->save();

                                $mediaId = $chatMedia->id;
                                Log::info('Saved ChatMedia record', ['media_id' => $mediaId]);
                            } else {
                                $mediaUrl = $parameter['value'];
                                Log::info('Using existing media url', ['url' => $mediaUrl]);
                            }

                            $metadata['header']['parameters'][] = [
                                'type' => $parameter['type'],
                                'selection' => $parameter['selection'],
                                'value' => $mediaUrl,
                            ];
                        }
                    }
                } else {
                    $metadata['header'] = $request->header;
                }

                $metadata['body'] = $request->body;
                $metadata['footer'] = $request->footer;
                $metadata['buttons'] = $request->buttons;
                $metadata['media'] = $mediaId;


                $scheduledAt = $request->skip_schedule ? Carbon::now('UTC') : Carbon::parse($request->time, $timezone)->setTimezone('UTC');
                Log::info('Scheduled time determined for campaign', ['scheduled_at' => $scheduledAt]);

                $campaign = new Campaign;
                $campaign['organization_id'] = $organizationId;
                $campaign['name'] = $request->name;
                $campaign['template_id'] = $template->id;
                $campaign['contact_group_id'] = $request->contacts === 'all' ? 0 : $contactGroup->id;
                $campaign['metadata'] = json_encode($metadata);
                $campaign['created_by'] = auth()->user()->id;
                $campaign['status'] = 'scheduled';
                $campaign['scheduled_at'] = $scheduledAt;
                $campaign->save();

                Log::info('Campaign created successfully', ['campaign_id' => $campaign->id]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to store campaign', [
                'error_message' => $e->getMessage(),
                'organization_id' => $organizationId,
                'template' => $request->template,
                'contacts' => $request->contacts,
                'user_id' => auth()->user()->id,
                'stack_trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function storeCarousel(Request $request)
    {
        $organizationId = session()->get('current_organization');

        // Validate required fields
        $request->validate([
            'payload.template.name' => 'required|string',
            'payload.to' => 'required|string',
            'campaign_name' => 'required|string',
        ]);

        // Extract data safely
        $uuid = $request['payload']['template']['name'];
        $contactId = $request['payload']['to'];

        Log::info('Storing campaign started', [
            'organization_id' => $organizationId,
            'template_uuid' => $uuid,
            'contacts' => $contactId,
            'request' => $request->all(),
        ]);

        $timezone = Setting::where('key', 'timezone')->value('value');
        $organization = Organization::find($organizationId);
        $organizationMetadata = json_decode($organization->metadata ?? '{}', true);
        $timezone = $organizationMetadata['timezone'] ?? $timezone;

        $template = Template::where('uuid', $uuid)->first();
        $contactGroup = ContactGroup::where('uuid', $contactId)->first();

        try {
            DB::transaction(function () use ($request, $organizationId, $template, $contactGroup, $timezone, $uuid, $contactId) {
                Log::info('Inside DB transaction for campaign creation');

                $mediaId = null;
                $metadata = [];

                // Handle header media if exists
                if (isset($request->header['format']) && in_array($request->header['format'], ['IMAGE', 'DOCUMENT', 'VIDEO'])) {
                    Log::info('Campaign has media header', ['format' => $request->header['format']]);
                    $header = $request->header;

                    if (!empty($header['parameters'])) {
                        $metadata['header']['format'] = $header['format'];
                        $metadata['header']['parameters'] = [];

                        foreach ($header['parameters'] as $key => $parameter) {
                            Log::info('Processing campaign header parameter', [
                                'key' => $key,
                                'selection' => $parameter['selection'] ?? null
                            ]);

                            if (($parameter['selection'] ?? '') === 'upload') {
                                $storage = Setting::where('key', 'storage_system')->value('local');
                                $file = $parameter['value'];
                                $fileName = $file->getClientOriginalName();

                                if ($storage === 'local') {
                                    $mediaFilePath = Storage::disk('local')->put('public', $file);
                                    $mediaUrl = rtrim(config('app.url'), '/') . '/media/' . ltrim($mediaFilePath, '/');
                                    Log::info('Stored campaign media locally', ['file' => $mediaFilePath]);
                                } else {
                                    $uploadedFile = $file->store('uploads/media/sent/' . $organizationId, 's3');
                                    $mediaUrl = Storage::disk('s3')->url($uploadedFile);
                                    Log::info('Stored campaign media on S3', ['file' => $uploadedFile]);
                                }

                                $contentType = $this->getContentTypeFromUrl($mediaUrl);
                                $mediaSize = $this->getMediaSizeInBytesFromUrl($mediaUrl);

                                $chatMedia = new ChatMedia;
                                $chatMedia->name = $fileName;
                                $chatMedia->path = $mediaUrl;
                                $chatMedia->type = $contentType;
                                $chatMedia->size = $mediaSize;
                                $chatMedia->created_at = now();
                                $chatMedia->save();

                                $mediaId = $chatMedia->id;
                                Log::info('Saved ChatMedia record', ['media_id' => $mediaId]);
                            } else {
                                $mediaUrl = $parameter['value'];
                                Log::info('Using existing media url', ['url' => $mediaUrl]);
                            }

                            $metadata['header']['parameters'][] = [
                                'type' => $parameter['type'] ?? 'image',
                                'selection' => $parameter['selection'] ?? 'url',
                                'value' => $mediaUrl,
                            ];
                        }
                    }
                } else {
                    $metadata['header'] = $request->header ?? [];
                }

                // Store message metadata
                $components = $request['payload']['template']['components'];
                Log::info('component is here: ' . json_encode($components, JSON_PRETTY_PRINT));


                $metadata['body'] = $request->body ?? [];
                $metadata['footer'] = $request->footer ?? [];
                $metadata['buttons'] = $request->buttons ?? [];
                $metadata['media'] = $mediaId;
                $metadata['carousel'] = $components;
                // $metadata['carousel'] = $components;

                // Log::info('metadata for campaign', ['metadata' => $metadata]);

                // Determine scheduling time
                $scheduledAt = $request->skip_schedule
                    ? Carbon::now('UTC')
                    : Carbon::parse($request->time, $timezone)->setTimezone('UTC');

                Log::info('Scheduled time determined for campaign', ['scheduled_at' => $scheduledAt]);



                // Create campaign
                $campaign = new Campaign;
                $campaign['organization_id'] = $organizationId;
                $campaign['name'] = $request->campaign_name;
                $campaign['template_id'] = $template->id ?? null;
                $campaign['contact_group_id'] = $contactId === 'all' ? 0 : ($contactGroup->id ?? 0);
                $campaign['metadata'] = json_encode($metadata);
                $campaign['created_by'] = auth()->user()->id;
                $campaign['status'] = 'scheduled';
                $campaign['scheduled_at'] = $scheduledAt;
                $campaign->save();

                Log::info('Campaign created successfully', ['campaign_id' => $campaign->id]);
            });

        } catch (\Exception $e) {
            Log::error('Failed to store campaign', [
                'error_message' => $e->getMessage(),
                'organization_id' => $organizationId,
                'template_uuid' => $uuid,
                'contact_uuid' => $contactId,
                'user_id' => auth()->user()->id,
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to store campaign', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Campaign stored successfully']);
    }



    private function getMediaInfo($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        Log::info('Getting media info', ['path' => $fullPath]);

        return [
            'name' => pathinfo($fullPath, PATHINFO_FILENAME),
            'type' => File::extension($fullPath),
            'size' => Storage::size($path),
        ];
    }

    public function sendCampaign()
    {
        Log::info('Dispatching SendCampaignJob');
        SendCampaignJob::dispatch();
    }

    public function destroy($uuid)
    {
        Log::info('Deleting campaign', ['uuid' => $uuid]);
        Campaign::where('uuid', $uuid)->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now()
        ]);
        Log::info('Campaign deleted', ['uuid' => $uuid]);
    }

    private function getContentTypeFromUrl($url)
    {
        try {
            Log::info('Fetching content type from url', ['url' => $url]);
            $response = Http::head($url);

            if ($response->hasHeader('Content-Type')) {
                $type = $response->header('Content-Type');
                Log::info('Content type fetched', ['url' => $url, 'content_type' => $type]);
                return $type;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching headers', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function getMediaSizeInBytesFromUrl($url)
    {
        $url = ltrim($url, '/');
        Log::info('Fetching media size from url', ['url' => $url]);
        $imageContent = @file_get_contents($url);

        if ($imageContent !== false) {
            $size = strlen($imageContent);
            Log::info('Fetched media size', ['url' => $url, 'size_bytes' => $size]);
            return $size;
        }

        Log::warning('Failed to fetch media size', ['url' => $url]);
        return null;
    }
}
