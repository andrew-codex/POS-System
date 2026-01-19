<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\DashboardController;
use App\Http\Controllers\Inventory\ProductsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CashierMiddleware;
use App\Http\Controllers\POS\SalesController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\POS\CartController;
use App\Http\Controllers\Inventory\StockController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Users\StaffController;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Controllers\POS\ReportsController;
use App\Http\Controllers\POS\RefundController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
Route::redirect('/', '/auth/login');
Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->name('auth.login.submit');




Route::middleware(['auth'])->group(function () {

  
    Route::middleware([PermissionMiddleware::class . ':view_dashboard,manage_products,view_products,view_categories,view_stock,manage_settings'])->group(function () {
    Route::put('/roles/{role}/update', [SettingsController::class, 'updateRolePermissions'])->name('roles.permissions.update');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('pos.dashboard');

      
        Route::get('/inventory/products', [ProductsController::class, 'index'])->name('inventory.products');
        Route::get('/inventory/products/create', [ProductsController::class, 'create'])->name('products.create');
        Route::post('/inventory/products', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/inventory/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
        Route::put('/inventory/products/{id}', [ProductsController::class, 'update'])->name('products.update');
        Route::delete('/inventory/products/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');

       
        Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('inventory.categories');
        Route::get('/inventory/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/inventory/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::delete('/inventory/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::put('/inventory/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
     
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/settings/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/settings/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/settings/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/settings/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/staff/{id}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggleStatus');
        Route::get('/settings/audit-logs', [SettingsController::class, 'auditlogs'])->name('settings.auditlogs');
        
        Route::get('pos/reports/pdf/sales-by-date', [ReportsController::class, 'exportSalesByDatePDF'])
         ->name('pos.reports.pdf.sales_by_date');

        Route::get('pos/reports/pdf/sales-by-product', [ReportsController::class, 'exportSalesByProductPDF'])
        ->name('pos.reports.pdf.sales_by_product');

         Route::get('pos/reports/pdf/invoice-details', [ReportsController::class, 'exportInvoiceDetailsPDF'])
         ->name('pos.reports.pdf.invoice_details');

        Route::get('/admin/logs', [ActivityLogController::class, 'index'])
        ->name('logs.index');


    });

    Route::get('/api/products/search', [CartController::class, 'searchProducts'])->name('api.products.search');
    Route::get('/api/sales/search', [SalesController::class, 'search'])->name('api.sales.search');
   
    Route::get('/sales', [SalesController::class, 'index'])
        ->middleware(PermissionMiddleware::class . ':view_sales')
        ->name('pos.sales');

    Route::post('/sales/store', [SalesController::class, 'store'])
        ->middleware(PermissionMiddleware::class . ':view_cart')
        ->name('pos.sales.store');

    Route::get('{sale}/refunds', [RefundController::class, 'index'])
    ->middleware(PermissionMiddleware::class . ':view_sales')
    ->name('sales.refunds.index');

    Route::post('{sale}/refunds', [RefundController::class, 'store'])
        ->middleware(PermissionMiddleware::class . ':view_sales')
        ->name('sales.refunds.store');

    Route::get('pos/reports', [ReportsController::class, 'index'])
        ->middleware(PermissionMiddleware::class . ':view_reports')
        ->name('pos.reports');

    
    Route::get('/cart', [CartController::class, 'index'])
        ->middleware(PermissionMiddleware::class . ':view_cart')
        ->name('pos.cart');


    Route::get('/inventory/stocks', [StockController::class, 'index'])
        ->middleware(PermissionMiddleware::class . ':view_stock')
        ->name('inventory.stock');
    Route::get('/inventory/stocks/create', [StockController::class, 'create'])
        ->middleware(PermissionMiddleware::class . ':view_stock')
        ->name('stock.create');
    Route::post('/inventory/stocks', [StockController::class, 'store'])
        ->middleware(PermissionMiddleware::class . ':view_stock')
        ->name('stock.store');
    Route::get('/inventory/stocks/{id}/edit', [StockController::class, 'edit'])
        ->middleware(PermissionMiddleware::class . ':view_stock')
        ->name('stock.edit');
    Route::put('/inventory/stocks/{id}', [StockController::class, 'update'])
        ->middleware(PermissionMiddleware::class . ':view_stock')
        ->name('stock.update');

    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
});