<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Helpers\SubscriptionHelper;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\Addon;
use App\Models\PaymentGateway;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use App\Services\SubscriptionPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SubscriptionController extends BaseController
{
    protected $billingService;
    protected $subscriptionService;
    protected $subscriptionPlanService;

    public function __construct()
    {
        Log::debug('Initializing SubscriptionController...');
        $this->billingService = new BillingService();
        $this->subscriptionService = new SubscriptionService();
        $this->subscriptionPlanService = new SubscriptionPlanService();
    }

    public function index(Request $request){
        Log::debug('SubscriptionController@index called', [
            'organizationId' => session()->get('current_organization'),
            'search' => $request->query('search')
        ]);

        $organizationId = session()->get('current_organization');
        $data['subscription'] = Subscription::with('plan')
            ->where('organization_id', $organizationId)
            ->first();
        Log::debug('Fetched subscription', ['subscription' => $data['subscription']]);

        $data['taxes'] = TaxRate::where('status', 'active')
            ->where('deleted_at', NULL)
            ->get();
        Log::debug('Fetched taxes', ['count' => $data['taxes']->count()]);

        $data['plans'] = SubscriptionPlanResource::collection(
            SubscriptionPlan::whereNull('deleted_at')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->query('search'). '%');
                })
                ->where('status', 'active')
                ->latest()
                ->paginate(10)
        );
        Log::debug('Fetched plans', ['count' => $data['plans']->count()]);

        $data['methods'] = $this->paymentMethods();
        Log::debug('Fetched payment methods', ['methods' => $data['methods']]);

        $data['subscriptionDetails'] = SubscriptionService::calculateSubscriptionBillingDetails(
            $organizationId,
            $data['subscription']->plan_id ?? null
        );
        Log::debug('Calculated subscription details', ['details' => $data['subscriptionDetails']]);

        $data['title'] = __('Billing');
        $data['addons'] = Addon::where('status', 1)
            ->where('is_plan_restricted', 1)
            ->pluck('is_active', 'name');
        Log::debug('Fetched addons', ['addons' => $data['addons']]);

        $data['enable_ai_billing'] = Setting::where('key', 'enable_ai_billing')->value('value') ?? 0;
        Log::debug('AI billing setting', ['enable_ai_billing' => $data['enable_ai_billing']]);

        return Inertia::render('User/Billing/Plan', $data);
    }

    public function store(Request $request){
        $userId = auth()->user()->id;
        $planId = $request->plan;
        $organizationId = session()->get('current_organization');

        Log::debug('SubscriptionController@store called', [
            'userId' => $userId,
            'planId' => $planId,
            'organizationId' => $organizationId
        ]);

        $response = SubscriptionService::store($request, $organizationId, $planId, $userId);
        Log::debug('SubscriptionService::store response', ['response' => $response]);

        if($response){
            if($response->success){
                Log::debug('Subscription stored successfully, redirecting', ['redirect_url' => $response->data]);
                return inertia::location($response->data);
            } else {
                Log::error('Subscription store failed', ['error' => $response->error]);
                return Redirect::back()->with(
                    'status', [
                        'type' => 'error', 
                        'message' => $response->error
                    ]
                );
            }
        } else {
            Log::debug('Subscription updated without redirect, redirecting to index');
            return Redirect::route('user.billing.index')->with(
                'status', [
                    'type' => 'success', 
                    'message' => __('Your subscription has been updated successfully!')
                ]
            );
        }
    }

    public function show($id)
    {
        $organizationId = session()->get('current_organization');
        Log::debug('SubscriptionController@show called', [
            'organizationId' => $organizationId,
            'planId' => $id
        ]);

        $details = SubscriptionService::calculateSubscriptionBillingDetails($organizationId, $id);
        Log::debug('Calculated subscription details for plan', ['details' => $details]);

        return Redirect::back()->with('response_data', [
            'data' => $details,
        ]);
    }

    public function applyCoupon(CouponRequest $request, $id)
    {
        $coupon = $request->input('coupon');
        session()->put('applied_coupon', $coupon);
        $organizationId = session()->get('current_organization');

        Log::debug('Coupon applied', [
            'coupon' => $coupon,
            'organizationId' => $organizationId,
            'planId' => $id
        ]);

        $details = SubscriptionService::calculateSubscriptionBillingDetails($organizationId, $id);
        Log::debug('Recalculated subscription details after coupon', ['details' => $details]);

        return Redirect::back()->with('response_data', [
            'data' => $details,
        ]);
    }

    public function removeCoupon(Request $request, $id)
    {
        session()->forget('applied_coupon');
        $organizationId = session()->get('current_organization');

        Log::debug('Coupon removed', [
            'organizationId' => $organizationId,
            'planId' => $id
        ]);

        $details = SubscriptionService::calculateSubscriptionBillingDetails($organizationId, $id);
        Log::debug('Recalculated subscription details after coupon removal', ['details' => $details]);

        return Redirect::back()->with('response_data', [
            'data' => $details,
        ]);
    }

    public function destroy($id)
    {
        Log::debug('SubscriptionController@destroy called', ['planId' => $id]);
        // Your logic for deleting a specific resource
    }

    private function paymentMethods(){
        Log::debug('Fetching payment methods...');

        $mergedData = [];

        $paymentMethods = PaymentGateway::where('is_active', 1)->get();
        Log::debug('Active payment gateways fetched', ['count' => $paymentMethods->count()]);

        $mergedData = $paymentMethods->map(function ($method) {
            return ['name' => $method->name];
        })->toArray();

        $activeAddons = Addon::where('category', 'payments')
            ->where('status', 1)
            ->get()
            ->where('is_active', 1)
            ->pluck('name')
            ->toArray();

        Log::debug('Active payment addons fetched', ['addons' => $activeAddons]);

        foreach ($activeAddons as $addonName) {
            $mergedData[] = ['name' => $addonName];
        }

        Log::debug('Merged payment methods and addons', ['mergedData' => $mergedData]);

        return $mergedData;
    }
}
