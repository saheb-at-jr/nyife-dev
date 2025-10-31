<?php

namespace App\Http\Controllers\User;

use App\Exports\CampaignDetailsExport;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreCampaign;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignLogResource;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Template;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log; // ✅ add Log facade
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;



class CampaignController extends BaseController
{


    public function storeCarousel(Request $request)
    {
        Log::debug('Storing campaign', ['request' => $request->all()]);

        $this->campaignService->storeCarousel($request);

        Log::info('Campaign created successfully');

        return Redirect::route('campaigns')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Campaign created successfully!')
            ]
        );
    }


    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(Request $request, $uuid = null)
    {
        $organizationId = session()->get('current_organization');
        Log::debug('CampaignController@index called', [
            'uuid' => $uuid,
            'organization_id' => $organizationId,
            'request_query' => $request->query(),
        ]);

        if ($uuid == null) {
            Log::debug('Fetching campaigns list');
            
            $searchTerm = $request->query('search');
            $settings = Organization::where('id', $organizationId)->first();

            $rows = CampaignResource::collection(
                Campaign::with(['template', 'campaignLogs'])
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at')
                    ->where(function ($query) use ($searchTerm) {
                        if ($searchTerm) {
                            $query->where('name', 'like', '%' . $searchTerm . '%')
                                ->orWhereHas('template', function ($templateQuery) use ($searchTerm) {
                                    $templateQuery->where('name', 'like', '%' . $searchTerm . '%');
                                });
                        }
                    })
                    ->latest()
                    ->paginate(10)
            );

            Log::debug('Campaigns fetched', ['count' => $rows->count()]);

            return Inertia::render('User/Campaign/Index', [
                'title' => __('Campaigns'),
                'allowCreate' => true,
                'rows' => $rows,
                'filters' => request()->all(['search']),
                'settings' => $settings
            ]);
        } else if ($uuid == 'create') {
            Log::debug('Creating new campaign form');

            $data['settings'] = Organization::where('id', $organizationId)->first();
            $data['templates'] = Template::where('organization_id', $organizationId)
                ->whereNull('deleted_at')
                ->where('status', 'APPROVED')
                ->get();
            $data['contactGroups'] = ContactGroup::where('organization_id', $organizationId)
                ->whereNull('deleted_at')
                ->get();
            $data['title'] = __('Create campaign');

            Log::debug('Campaign create form data prepared', [
                'templates_count' => $data['templates']->count(),
                'groups_count' => $data['contactGroups']->count(),
            ]);

            return Inertia::render('User/Campaign/Create', $data);
        } else {
            Log::debug('Viewing campaign details', ['uuid' => $uuid]);

            $data['campaign'] = Campaign::with('contactGroup', 'template')->where('uuid', $uuid)->first();

            if ($data['campaign']) {
                $counts = $data['campaign']->getCounts();
                Log::debug('Campaign found', [
                    'id' => $data['campaign']->id,
                    'counts' => $counts
                ]);

                $data['campaign']['total_message_count'] = $counts->total_message_count ?? 0;
                $data['campaign']['total_sent_count'] = $counts->total_sent_count ?? 0;
                $data['campaign']['total_delivered_count'] = $counts->total_delivered_count ?? 0;
                $data['campaign']['total_failed_count'] = $counts->total_failed_count ?? 0;
                $data['campaign']['total_read_count'] = $counts->total_read_count ?? 0;
            } else {
                Log::warning('Campaign not found', ['uuid' => $uuid]);

                $data['campaign']['total_message_count'] = 0;
                $data['campaign']['total_sent_count'] = 0;
                $data['campaign']['total_delivered_count'] = 0;
                $data['campaign']['total_read_count'] = 0;
                $data['campaign']['total_failed_count'] = 0;
            }

            $searchTerm = $request->query('search');
            $data['filters'] = request()->all(['search']);

            $data['rows'] = CampaignLogResource::collection(
                CampaignLog::with('contact', 'chat.logs')
                    ->where('campaign_id', $data['campaign']->id ?? 0)
                    ->where(function ($query) use ($searchTerm) {
                        if ($searchTerm) {
                            $query->whereHas('contact', function ($contactQuery) use ($searchTerm) {
                                $contactQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                    ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                            });
                        }
                    })
                    ->orderBy('id')
                    ->paginate(10)
            );

            Log::debug('Campaign logs fetched', ['logs_count' => $data['rows']->count()]);

            $data['title'] = __('View campaign');

            return Inertia::render('User/Campaign/View', $data);
        }
    }

    public function store(StoreCampaign $request)
    {
        Log::debug('Storing campaign', ['request' => $request->all()]);

        $this->campaignService->store($request);

        Log::info('Campaign created successfully');

        return Redirect::route('campaigns')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Campaign created successfully!')
            ]
        );
    }

    

    public function export($uuid = null)
    {
        Log::debug('Exporting campaign details', ['uuid' => $uuid]);

        return Excel::download(new CampaignDetailsExport($uuid), 'campaign.csv');
    }

    public function delete($uuid)
    {
        Log::debug('Deleting campaign', ['uuid' => $uuid]);

        $this->campaignService->destroy($uuid);

        Log::info('Campaign deleted successfully', ['uuid' => $uuid]);

        return Redirect::back()->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Row deleted successfully!')
            ]
        );
    }
}
