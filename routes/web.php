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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

// Legal Pages
Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'legal.terms-of-service')->name('terms.service');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth.users')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('farms', FarmController::class);
    Route::resource('houses', HouseController::class);
    Route::resource('batches', BirdBatchController::class);
    Route::resource('fields', FieldController::class);
    Route::resource('plantings', PlantingController::class);
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Egg Production & Sales
    Route::resource('egg-productions', EggProductionController::class);
    Route::resource('egg-sales', EggSaleController::class);
    Route::resource('bird-sales', BirdSaleController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

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

    // Employees (Admin/Manager only)
    Route::middleware('employee.access:manager')->group(function () {
        Route::resource('employees', EmployeeController::class);
    });
});