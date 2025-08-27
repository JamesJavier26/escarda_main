<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MouldController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PricingEstimateController;
use App\Http\Controllers\ProductDetailReportController;
use App\Http\Controllers\ProductionDetailController;
use App\Http\Controllers\ProductionDetailEntryController;
use App\Http\Controllers\ProductionDetailReportController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TemporaryQuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Models\PricingEstimate;
use App\Models\ProductDetailReport;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::redirect('/', '/dashboard')->name('home');
    Route::get('/run-migrate', function () {
        Artisan::call('migrate:fresh', ['--force' => true]); // optional: change to 'migrate' if you don't want to wipe tables
        Artisan::call('db:seed', ['--force' => true]);
        return 'Migration and seeding complete.';
    });
    Route::get('/link-storage', function () {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (!File::exists($link)) {
            File::copyDirectory($target, $link);
            return 'Storage files copied manually.';
        }

        return 'Storage folder already exists.';
    });
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');
// Route::middleware(['restrict.ip'])->group(function () {
//     Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard');
// });
Route::middleware(['auth'])->group(function () {

    // Route::middleware(['role:super-admin'])->group(function () {

        Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard');

        Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
        Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
        Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
        Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
        Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
        Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::post('/messages/start-direct', [MessageController::class, 'startDirect'])->name('messages.start-direct');
        Route::post('/messages/create-group', [MessageController::class, 'createGroup'])->name('messages.create-group');
        Route::get('/messages/{conversationId?}', [MessageController::class, 'index'])->name('messages.index');
        Route::delete('/messages/{conversation}/leave', [MessageController::class, 'leave'])->name('messages.leave');
        Route::get('/messages/{conversation}/members', [MessageController::class, 'members'])->name('messages.members');
        Route::post('/messages/{conversation}/manage-users', [MessageController::class, 'manageUsers'])->name('messages.manage-users');


        Route::get('/messages/{conversation}/fetch', [MessageController::class, 'fetch'])->name('messages.fetch');

        // save a message (text / file / image)
        Route::post('/messages/{conversation}',  [MessageController::class, 'store'])->name('messages.store');



        Route::resource('pricing-estimates', PricingEstimateController::class);
        Route::patch('/pricing-estimate-items/{item}/update-unit-price', [PricingEstimateController::class, 'updateUnitPrice'])->name('pricing-estimate-items.update-unit-price');
        Route::patch('/pricing-estimate-items/{item}/update-remarks', [PricingEstimateController::class, 'updateRemarks'])->name('pricing-estimate-items.update-remarks');

        


        Route::resource('sales-orders', SalesOrderController::class);
        Route::post('/sales-orders/comments', [SalesOrderController::class, 'storeComment'])->name('sales-order.comments.store');
        Route::patch('/sales-order/{sales_order}/submit', [SalesOrderController::class, 'submitForApproval'])->name('sales-order.submit');
        // Route::post('/sales-order/{sales_order_id}/approve', [SalesOrderController::class, 'approve'])->name('pricing-estimates.approve');
        Route::post('/sales-order/{sales_order_id}/reject', [SalesOrderController::class, 'reject'])->name('sales-orders.reject');



        Route::resource('job-orders', JobOrderController::class);
        Route::post('/job-orders/{job_order}/activate', [JobOrderController::class, 'activate'])->name('job-orders.activate');
        // Route::get('/job-orders/search', [JobOrderController::class, 'search'])->name('job-orders.search');
        Route::patch('/job-orders/{job_order}/mark-delivered', [JobOrderController::class, 'markAsDelivered'])->name('job-orders.mark-delivered');
        Route::patch('/job-orders/{job_order}/approve', [JobOrderController::class, 'approve'])->name('job-orders.approve');
        Route::patch('/job-orders/{job_order}/cancel', [JobOrderController::class, 'cancel'])->name('job-orders.cancel');
        Route::get('/job-orders/items/{id}/details', [JobOrderController::class, 'itemDetails'])
                ->name('job-orders.items.details');

        Route::get('/api/job-orders/search', [JobOrderController::class, 'search']);
        Route::get('/api/job-orders/{jobOrder}/items', [JobOrderController::class, 'getItems']);

       // Main resource routes
        Route::resource('production-detail-reports', ProductionDetailReportController::class);

        // Custom submit (PATCH on report)
        Route::patch('production-detail-reports/{report}/submit', [ProductionDetailReportController::class, 'submit'])
            ->name('production-detail-reports.submit');

        // Nested routes for entries
        Route::get('production-detail-reports/{production_detail_report}/entries', [ProductionDetailEntryController::class, 'index'])
            ->name('production-detail-reports.entries.index');

        Route::post('production-detail-reports/{production_detail_report}/entries', [ProductionDetailEntryController::class, 'store'])
            ->name('production-detail-reports.entries.store');

        // Entry updates & deletes (separate resource-like endpoints)
        Route::put('production-detail-entries/{production_detail_entry}', [ProductionDetailEntryController::class, 'update'])
            ->name('production-detail-entries.update');

        Route::delete('production-detail-entries/{production_detail_entry}', [ProductionDetailEntryController::class, 'destroy'])
            ->name('production-detail-entries.destroy');


        Route::resource('temporary_quotations', TemporaryQuotationController::class);
        Route::get('/temporary-quotations/{id}/pdf', [TemporaryQuotationController::class, 'generateTemporaryQuote'])
            ->name('temporary_quotations.pdf');


        // MASTER DATA
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::resource('currencies', CurrencyController::class)->except(['show']);
        Route::resource('moulds', MouldController::class)->except(['show']);
        Route::resource('materials', MaterialController::class)->except(['show']);
       
        Route::resource('units', UnitController::class)->except('show');

        Route::resource('employees', EmployeeController::class)->except('show');
        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('positions', PositionController::class)->except('show');

         Route::resource('items', ItemController::class)->except(['show']);
        Route::prefix('items')->group(function () {
            Route::post('quick-add',  [ItemController::class, 'quickAdd'])->name('items.quick-add');
            Route::get ('search',     [ItemController::class, 'search'])   ->name('items.search');
        });
        Route::post('/item-categories', [ItemController::class, 'store_category'])->name('item-categories.store');
        Route::get('/api/item-categories', fn() => \App\Models\ItemCategory::orderBy('name')->get());

        Route::get('/api/customers', function () {
            $term = request('q', '');
            return \App\Models\Customer::where('name', 'like', "%$term%")
                ->orWhere('tin_no', 'like', "%$term%")
                ->limit(10)
                ->get(['id', 'name', 'address', 'email', 'telno', 'tin_no', 'website', 'tax_status', 'terms_of_payment']);
        });

        Route::get('/api/items', function () {
            $q = request('q', '');
            return \App\Models\Item::query()
                ->where('name',   'like', "%{$q}%")
                ->orWhere('number','like', "%{$q}%")
                ->limit(5)
                ->get(['id','number','unit_id','name','description']);
        });


        // Reports
        Route::get('generate-quote/{pe_no}', [ReportController::class, 'generateQuote'])->name('reports.quotations');
        Route::get('generate-sales/{so_no}', [ReportController::class, 'generateSales'])->name('reports.salesOrder');
        Route::get('generate-invoice/{so_no}', [ReportController::class, 'generateInvoice'])->name('reports.invoice');
        Route::get('generate-job-order/{jo_no}', [ReportController::class, 'generateJO'])->name('reports.jobOrder');
        Route::get('generate-production-detail-report', [ReportController::class, 'generatePDR'])->name('reports.pdr');
        Route::get('/sales-order/{order}/export-sales-invoice', [ReportController::class, 'exportInvoice'])->name('reports.export-sales-invoice');
        Route::get('/sales-order/{order}/export-billing-invoice', [ReportController::class, 'exportBillingInvoice'])->name('reports.export-billing-invoice');
        Route::get('/sales-order/{order}/export-charge-invoice', [ReportController::class, 'exportChargeInvoice'])->name('reports.export-charge-invoice');


        Route::resource('quotations',QuotationController::class);
        Route::get('/quotations/generate/{pricing_estimate}', [QuotationController::class, 'generate'])->name('quotations.generate');
        Route::post('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('/quotations/{quotation}/export-to-sales-order', [QuotationController::class, 'exportToSalesOrder'])->name('quotations.export-to-sales-order');
        Route::patch('/quotations/{quotation}/toggle-sent', [QuotationController::class, 'toggleSentStatus'])->name('quotations.toggleSent');
        
        Route::get('quotations/items/{pe_id}', [QuotationController::class,'quote_items'])->name('quotations.items');
        
        Route::get('/pricing-estimates/{pe}/items', [PricingEstimateController::class,'items'])->name('pricing-estimates.items');
        //pending P.E's
        Route::get('/pricing-estimates/my/estimates', [PricingEstimateController::class, 'my_estimates'])->name('pricing-estimates.my');
        Route::get('/pricing-estimates/{pe_id}/price-entry', [PricingEstimateController::class, 'priceEntry'])->name('pricing-estimates.price-entry');
        Route::post('/pricing-estimates/{id}/save-delivery-date', [PricingEstimateController::class, 'saveDeliveryDate'])
            ->name('pricing-estimates.save-delivery-date');
        Route::post('/pricing-estimate-add-offer', [PricingEstimateController::class, 'addOffer']);
        Route::delete('/pricing-estimates/{estimate}', [PricingEstimateController::class, 'destroy'])
            ->name('pricing-estimates.destroy');
        Route::delete('/pricing-estimates/item/{item}', [PricingEstimateController::class, 'destroyItem'])
            ->name('pricing-estimate-item.destroy');
            

        // });
        Route::patch('/pricing-estimate-items/{item}/apply-discount', [PricingEstimateController::class, 'applyDiscount']);
        Route::delete('/pricing-estimate-items/{item}', [PricingEstimateController::class, 'destroyItem'])->name('pe-items.destroy');
        Route::patch('/pricing-estimates/{pricing_estimate}/submit', [PricingEstimateController::class, 'submitForApproval'])->name('pricing-estimates.submit');
        // Route::post('/pricing-estimates/{pricing_estimate_id}/approve', [PricingEstimateController::class, 'approve'])->name('pricing-estimates.approve');
        Route::post('/pricing-estimates/{pricing_estimate_id}/reject', [PricingEstimateController::class, 'reject'])->name('pricing-estimates.reject');
        Route::patch('/pricing-estimates/{pricing_estimate}/cancel', [PricingEstimateController::class, 'cancel'])->name('pricing-estimates.cancel');


        // Extras
        Route::post('/notifications/{notification}/mark-read', function (\App\Models\Notification $notification) {
            $notification->update(['is_read' => true]);
            return response()->noContent();
        });
        Route::get('/test', function (\App\Models\Notification $notification){

        })->name('notifications.index');

        Route::resource('activities', ActivityLogController::class);
        Route::prefix('api')->group(function () {
            Route::get('/business-types', [CustomerController::class, 'getBusinessTypes']);
            Route::post('/business-types', [CustomerController::class, 'storeBusinessType']);

            Route::get('/cities', [AddressController::class, 'getCitiesByProvince']);
            Route::get('/barangays', [AddressController::class, 'getBarangaysByCity']);

            // Get single city
            Route::get('/cities/{city}', [AddressController::class, 'getCity']);

            // Get single barangay
            Route::get('/barangays/{barangay}', [AddressController::class, 'getBarangay']);
        });

});

require __DIR__.'/auth.php';
