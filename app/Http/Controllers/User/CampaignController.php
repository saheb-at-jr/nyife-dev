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
use App\Models\BalanceHistory;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CampaignController extends BaseController
{
    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(Request $request, $uuid = null){
        $organizationId = session()->get('current_organization');
        if ($uuid == null) {
            $searchTerm = $request->query('search');
            $settings = Organization::where('id', $organizationId)->first();
            $rows = CampaignResource::collection(
                Campaign::with(['template', 'campaignLogs'])
                    ->where('organization_id', $organizationId)
                    ->where('deleted_at', null)
                    ->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%')
                              ->orWhereHas('template', function ($templateQuery) use ($searchTerm) {
                                  $templateQuery->where('name', 'like', '%' . $searchTerm . '%');
                              });
                    })
                    ->latest()
                    ->paginate(10)
            );

            return Inertia::render('User/Campaign/Index', [
                'title'=> __('Campaigns'),
                'allowCreate' => true,
                'rows' => $rows,
                'filters' => request()->all(['search']),
                'settings' => $settings
            ]);
        } else if ($uuid == 'create') {
            $data['settings'] = Organization::where('id', $organizationId)->first();
            $data['templates'] = Template::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->where('status', 'APPROVED')
                ->get();

            $data['contactGroups'] = ContactGroup::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->get();

            $data['title'] = __('Create campaign');

            return Inertia::render('User/Campaign/Create', $data);
        } else {
            $data['campaign'] = Campaign::with('contactGroup', 'template')->where('uuid', $uuid)->first();

            if ($data['campaign']) {
                $counts = $data['campaign']->getCounts();
                $data['campaign']['total_message_count'] = $counts->total_message_count ?? 0;
                $data['campaign']['total_sent_count'] = $counts->total_sent_count ?? 0;
                $data['campaign']['total_delivered_count'] = $counts->total_delivered_count ?? 0;
                $data['campaign']['total_failed_count'] = $counts->total_failed_count ?? 0;
                $data['campaign']['total_read_count'] = $counts->total_read_count ?? 0;
            } else {
                $data['campaign']['total_message_count'] = 0;
                $data['campaign']['total_sent_count'] = 0;
                $data['campaign']['total_delivered_count'] = 0;
                $data['campaign']['total_failed_count'] = 0;
                $data['campaign']['total_read_count'] = 0;
            }

            $data['filters'] = request()->all(['search']);

            $searchTerm = $request->query('search');
            $data['rows'] = CampaignLogResource::collection(
                CampaignLog::with('contact', 'chat.logs')
                    ->where('campaign_id', $data['campaign']->id)
                    ->where(function ($query) use ($searchTerm) {
                        $query->whereHas('contact', function ($contactQuery) use ($searchTerm) {
                            $contactQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                        });
                    })
                    ->orderBy('id')
                    ->paginate(10)
            );
            $data['title'] = __('View campaign');

            return Inertia::render('User/Campaign/View', $data);
        }
    }

    public function store(StoreCampaign $request){
        $templateUuid = $request->input('template');
        $templateCategory = null;

        if ($templateUuid) {
            $template = \App\Models\Template::where('uuid', $templateUuid)->first();
            $templateCategory = $template ? $template->category : null;
        }

        $contactsGroupUuid = $request->input('contacts');
        $contactsCount = null;

        if ($contactsGroupUuid) {
            $group = \App\Models\ContactGroup::where('uuid', $contactsGroupUuid)->first();
            $contactsCount = $group ? $group->contacts()->count() : 0;
        }

        $userId = method_exists($request, 'user') && $request->user()
                    ? $request->user()->id
                    : auth()->id();

        $user = $userId ? \App\Models\User::find($userId) : null;

        if (!$user || $user->balance < 1) {
            return Redirect::route('campaigns')->with('status', [
                'type' => 'error',
                'message' => __('Insufficient balance to create campaign.')
            ]);
        }

        $marketingPrice = (float) ($user->marketing_price ?? 0);
        $utilityPrice   = (float) ($user->utility_price ?? 0);
        $authPrice      = (float) ($user->auth_price ?? 0);
        $contactsCount  = (int) ($contactsCount ?? 0);

        $category = strtolower((string) $templateCategory);
        switch ($category) {
            case 'marketing': $perContactPrice = $marketingPrice; break;
            case 'utility':   $perContactPrice = $utilityPrice;   break;
            case 'auth':      $perContactPrice = $authPrice;      break;
            default:          $perContactPrice = $marketingPrice; break;
        }

        $calculatedCharge = $perContactPrice * $contactsCount;
        $charge = round($calculatedCharge, 2);

        $oldBalance = (float) $user->balance;
        $newBalance = round($oldBalance - $charge, 2);

        $user->balance = $newBalance;
        $user->save();

        BalanceHistory::create([
            'user_id' => $userId,
            'amount' => -$charge,
            'balance_after' => $newBalance,
            'type' => 'debit',
            'note' => "Campaign charge for {$contactsCount} contacts"
        ]);

        $this->campaignService->store($request);

        return Redirect::route('campaigns')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Campaign created successfully!')
            ]
        );
    }

    public function export($uuid = null){
        return Excel::download(new CampaignDetailsExport($uuid), 'campaign.csv');
    }

    public function delete($uuid){
        $this->campaignService->destroy($uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Row deleted successfully!')
            ]
        );
    }

    public function storeCarousel(Request $request)
    {
        
        $this->campaignService->storeCarousel($request);

        return Redirect::route('campaigns')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Campaign created successfully!')
            ]
        );
    }
}
