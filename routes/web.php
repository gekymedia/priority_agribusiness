<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\BirdBatchController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\PlantingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EggProductionController;
use App\Http\Controllers\EggSaleController;
use App\Http\Controllers\BirdSaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\MedicationCalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\AiAnalyticsController;
use App\Http\Controllers\BirdMortalityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EggStoreController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Guest Routes (Public)
Route::get('/events', function () {
    return view('events.index');
})->name('events.index');

// Public egg store (no auth)
Route::get('/store', [EggStoreController::class, 'index'])->name('store.index');
Route::post('/store/cart', [EggStoreController::class, 'addToCart'])->name('store.add-to-cart');
Route::get('/store/cart', [EggStoreController::class, 'cart'])->name('store.cart');
Route::put('/store/cart', [EggStoreController::class, 'updateCart'])->name('store.cart.update');
Route::get('/store/cart/remove/{unitType}', [EggStoreController::class, 'removeFromCart'])->name('store.cart.remove');
Route::get('/store/checkout', [EggStoreController::class, 'checkout'])->name('store.checkout');
Route::post('/store/checkout', [EggStoreController::class, 'processCheckout'])->name('store.checkout.process');
Route::get('/store/payment/callback', [EggStoreController::class, 'paymentCallback'])->name('store.payment.callback');
Route::get('/store/order/{order}/pending', [EggStoreController::class, 'orderPending'])->name('store.order.pending');
Route::get('/store/order/{order}/success', [EggStoreController::class, 'orderSuccess'])->name('store.order.success');

// Legal Pages
Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'legal.terms-of-service')->name('terms.service');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public registration disabled - only admins can add users/employees
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth.users')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('farms', FarmController::class);
    Route::resource('houses', HouseController::class);
    Route::resource('batches', BirdBatchController::class);
    Route::resource('fields', FieldController::class);
    Route::resource('plantings', PlantingController::class);
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Egg Production & Sales
    Route::get('egg-productions/bulk-import', [EggProductionController::class, 'bulkImport'])->name('egg-productions.bulk-import');
    Route::post('egg-productions/bulk-import', [EggProductionController::class, 'processBulkImport'])->name('egg-productions.bulk-import.process');
    Route::get('egg-productions/data', [EggProductionController::class, 'data'])->name('egg-productions.data');
    Route::resource('egg-productions', EggProductionController::class);
    Route::resource('egg-sales', EggSaleController::class);
    Route::post('egg-sales/online-orders/{market_order}/complete', [EggSaleController::class, 'markOrderComplete'])->name('egg-sales.online-orders.complete');
    Route::resource('bird-sales', BirdSaleController::class);
    Route::resource('bird-mortality', BirdMortalityController::class)->except(['show']);

    // Expenses
    Route::get('expenses/bulk-add', [ExpenseController::class, 'bulkAdd'])->name('expenses.bulk-add');
    Route::post('expenses/bulk-add', [ExpenseController::class, 'storeBulk'])->name('expenses.bulk-add.store');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

    // Account & Finance (Income, Expenditure, bank sync)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::prefix('income')->name('income.')->group(function () {
            Route::get('/', [FinanceController::class, 'incomeIndex'])->name('index');
            Route::get('/create', [FinanceController::class, 'incomeCreate'])->name('create');
            Route::post('/', [FinanceController::class, 'incomeStore'])->name('store');
            Route::post('/{income}/sync', [FinanceController::class, 'incomeSync'])->name('sync');
        });
        Route::prefix('expenditure')->name('expenditure.')->group(function () {
            Route::get('/', [FinanceController::class, 'expenditureIndex'])->name('index');
            Route::post('/{expense}/sync', [FinanceController::class, 'expenditureSync'])->name('sync');
        });
    });

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Medication Calendars
    Route::get('/medication-calendars', [MedicationCalendarController::class, 'index'])->name('medication-calendars.index');
    Route::get('/medication-calendars/{medicationCalendar}', [MedicationCalendarController::class, 'show'])->name('medication-calendars.show');
    Route::get('/batches/{batch}/assign-medication', [MedicationCalendarController::class, 'assignForm'])->name('batches.assign-medication');
    Route::post('/batches/{batch}/assign-medication', [MedicationCalendarController::class, 'assign'])->name('batches.assign-medication.store');
    Route::get('/batches/{batch}/medication-schedule', [MedicationCalendarController::class, 'viewSchedule'])->name('batches.medication-schedule');
    Route::post('/medication-schedules/{schedule}/complete', [MedicationCalendarController::class, 'completeSchedule'])->name('medication-schedules.complete');

    // AI Analytics
    Route::get('/ai-analytics', [AiAnalyticsController::class, 'index'])->name('ai-analytics.index');
    Route::post('/ai-analytics/analyze', [AiAnalyticsController::class, 'analyze'])->name('ai-analytics.analyze');

    // Stop impersonation (available to anyone who is impersonating)
    Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');

    // Employees (Admin/Manager only)
    Route::middleware('employee.access:manager')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::patch('/employees/{employee}/approve', [EmployeeController::class, 'approve'])->name('employees.approve');
        
        // User management
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // Impersonation start (Admin only)
        Route::post('/impersonate/{employee}', [ImpersonationController::class, 'start'])->name('impersonate.start');
        Route::post('/impersonate/user/{user}', [ImpersonationController::class, 'startUser'])->name('impersonate.user');
    });

    // Payroll (Admin/Manager only)
    Route::middleware('employee.access:manager')->group(function () {
        Route::resource('payroll', PayrollController::class);
        Route::post('/payroll/{payroll}/status', [PayrollController::class, 'updateStatus'])->name('payroll.status');
    });

    // Payslips (for employees to view their own payslips)
    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslips/{payroll}', [PayslipController::class, 'show'])->name('payslips.show');

    // Settings (Admin only)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/test-notification', [SettingsController::class, 'testNotification'])->name('settings.test-notification');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('/settings/notification-settings', [SettingsController::class, 'updateNotificationSettings'])->name('settings.notification-settings');
    Route::put('/settings/priority-bank', [SettingsController::class, 'updatePriorityBank'])->name('settings.priority-bank.update');
    Route::get('/payment-settings', [PaymentSettingsController::class, 'index'])->name('payment-settings.index');
    Route::put('/payment-settings', [PaymentSettingsController::class, 'update'])->name('payment-settings.update');

    // System Logs (Laravel log files)
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::post('/refresh', [LogController::class, 'refresh'])->name('refresh');
        Route::post('/clear', [LogController::class, 'clear'])->name('clear');
        Route::post('/clear-all', [LogController::class, 'clearAll'])->name('clear-all');
        Route::get('/download', [LogController::class, 'download'])->name('download');
        Route::get('/download-all', [LogController::class, 'downloadAll'])->name('download-all');
    });
});