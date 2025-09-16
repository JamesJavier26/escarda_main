<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\PricingEstimate;
use App\Models\ProductionDetailReport;
use App\Models\Quotation;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $user = Auth::user();

    if ($user->hasRole(['super-admin', 'admin', 'sales-manager', 'operations-manager'])) {
        return $this->admin_dashboard();
    } elseif ($user->hasRole(['operation-staff', 'production-staff'])) {
        // redirect production/operation staff to production report index
        return redirect()->route('production-detail-reports.index');
    } else {
        return $this->sales_dashboard();
    }
        

    // Optional: Handle other roles or fallback
    abort(403, 'Unauthorized');
}
    public function admin_dashboard()
    {
        if (! Auth::user()->hasAnyRole(['super-admin', 'admin'])) {
         return redirect()->route('pricing-estimates.my');
        }
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyRevenue = SalesOrder::with('jobOrder.jo_items')
            ->where('so_status', 'approved') 
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum(function ($order) {
                if (!$order->jobOrder || !$order->jobOrder->jo_items) return 0;

                return $order->jobOrder->jo_items->sum(function ($item) {
                    return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
                });
            });
        return view('dashboard.admin-dashboard', [
            'pendingEstimatesCount' => PricingEstimate::where('pe_status', 'pending')->count(),
            'newEstimatesToday' => PricingEstimate::whereDate('created_at', today())->count(),
            'approvedQuotesCount' => Quotation::where('quote_status', 'approved')->count(),
            'quotesReadyForPO' => Quotation::where('quote_status', 'approved')->where('is_final', true)->doesntHave('salesOrder')->count(),
            'monthlyRevenue' => $monthlyRevenue,
            'revenueChange' => 0,
            'activeProjectsCount' => 0,
            'overdueProjects' => 0,
            
            'pendingEstimates' => PricingEstimate::with(['customer', 'salesRep'])
                ->where('pe_status', 'pending')
                ->latest()
                ->limit(5)
                ->get(),
                
            'pendingSalesOrder' => SalesOrder::with(['customer', 'currency'])
                ->where('so_status', 'pending')
                ->latest()
                ->limit(5)
                ->get(),
                
            'pendingJobOrders' => JobOrder::with(['salesOrder.customer'])
                ->where('jo_status', 'pending')
                ->latest()
                ->limit(5)
                ->get(),
                
        'recentProductionReports' => ProductionDetailReport::with(['jobOrder', 'item', 'customer'])
            ->where('pdr_status','completed')
            ->latest()
            ->limit(5)
            ->get(),
            
            'recentActivities' => Activity::with('causer') // eager load causer
            ->latest()
            ->limit( 7)
            ->get()
            ->map(function ($activity) {
                $icon = match ($activity->description) {
                    'created' => ['icon' => 'fas fa-plus', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'],
                    'updated' => ['icon' => 'fas fa-edit', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'],
                    'deleted' => ['icon' => 'fas fa-trash', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'],
                    default => ['icon' => 'fas fa-info-circle', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                };

                return [
                    'description' => ucfirst($activity->description) . ' ' . class_basename($activity->subject_type ?? 'Record'),
                    'timestamp' => Carbon::parse($activity->created_at)->diffForHumans(),
                    'user' => $activity->causer?->username ?? 'System',
                    'icon' => $icon['icon'],
                    'color' => $icon['color'],
                ];
            })
        ]);
    }
    public function sales_dashboard()
    {
        return view('dashboard.sales-dashboard', [
            'monthlySales' => 100,
            'monthlyTarget' => 100,
            'targetAchievement' => 100,
            'salesChange' => 100,
            'newCustomersCount' => 100,
            'newCustomersThisMonth' => 100,
            'conversionRate' => 100,
            'topCustomers' => [],
            'recentSalesOrders' => [],
            'pendingEstimates' => [],
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
