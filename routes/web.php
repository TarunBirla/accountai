<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminJourneyController;
use App\Http\Controllers\Admin\AdminLeadController;
use App\Http\Controllers\Admin\AdminReminderController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientJourneyRequestController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\PublicLeadController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AccountAI Lead & Practice Management
|--------------------------------------------------------------------------
*/

// One-time Automated Setup routes for shared hosting & live server
Route::get('/setup/run-all', [SetupController::class, 'runAll'])->name('setup.run-all');
Route::get('/setup/install', [SetupController::class, 'install'])->name('setup.install');

// Public Frontend & Lead Generation
Route::get('/', [PublicLeadController::class, 'home'])->name('home');
Route::get('/social', [PublicLeadController::class, 'social'])->name('social');
Route::get('/services/{slug}', [PublicLeadController::class, 'serviceDetail'])->name('services.show');

// Lead Capture with Honeypot & Rate Limiting (5 submissions per min)
Route::post('/lead/website', [PublicLeadController::class, 'storeWebsite'])
    ->middleware('throttle:5,1')
    ->name('lead.website');

Route::post('/lead/social', [PublicLeadController::class, 'storeSocial'])
    ->middleware('throttle:5,1')
    ->name('lead.social');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Client Portal Routes (Role: client)
Route::prefix('client')->middleware(['auth', 'role:client'])->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [ClientJourneyRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{stepId}', [ClientJourneyRequestController::class, 'submit'])->name('requests.submit');
    Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile');
    Route::post('/profile/password', [ClientProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin Panel Routes (Role: admin)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Leads Management & Export
    Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [AdminLeadController::class, 'exportCsv'])->name('leads.export');
    Route::get('/leads/{id}', [AdminLeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{id}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
    Route::patch('/leads/{id}/notes', [AdminLeadController::class, 'updateNotes'])->name('leads.notes');

    // Client Journey Timeline & Stage Review
    Route::get('/journey', [AdminJourneyController::class, 'index'])->name('journey.index');
    Route::get('/journey/{leadId}', [AdminJourneyController::class, 'show'])->name('journey.show');
    Route::post('/journey/steps/{stepId}/review', [AdminJourneyController::class, 'reviewStep'])->name('journey.steps.review');
    Route::patch('/journey/steps/{stepId}/status', [AdminJourneyController::class, 'updateStepStatus'])->name('journey.steps.status');

    // Reminders
    Route::get('/reminders', [AdminReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminders', [AdminReminderController::class, 'store'])->name('reminders.store');
    Route::patch('/reminders/{id}/toggle', [AdminReminderController::class, 'toggleDone'])->name('reminders.toggle');
    Route::delete('/reminders/{id}', [AdminReminderController::class, 'destroy'])->name('reminders.destroy');

    // Services CRUD
    Route::resource('services', AdminServiceController::class);
});
