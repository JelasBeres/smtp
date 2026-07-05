<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\EmailProviderController;
use App\Http\Controllers\ImportHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendingDomainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuppressionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WebhookLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('role:super_admin');
    Route::resource('contacts', ContactController::class)->only(['index', 'store', 'destroy'])->middleware('role:super_admin,admin,operator');
    Route::post('/contacts/import', [ContactController::class, 'import'])->middleware('role:super_admin,admin,operator')->name('contacts.import');
    Route::resource('templates', TemplateController::class)->only(['index', 'store'])->middleware('role:super_admin,admin');
    Route::resource('campaigns', CampaignController::class)->only(['index', 'store'])->middleware('role:super_admin,admin,operator');
    Route::post('/campaigns/{campaign}/schedule', [CampaignController::class, 'schedule'])->name('campaigns.schedule');
    Route::post('/campaigns/{campaign}/start', [CampaignController::class, 'start'])->name('campaigns.start');
    Route::post('/campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('/campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
    Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::resource('suppressions', SuppressionController::class)->only(['index', 'store'])->middleware('role:super_admin,admin');
    Route::resource('sending-domains', SendingDomainController::class)->only(['index', 'store'])->middleware('role:super_admin');
    Route::post('/sending-domains/{domain}/check', [SendingDomainController::class, 'check'])->name('sending-domains.check');
    Route::resource('email-providers', EmailProviderController::class)->only(['index', 'store'])->middleware('role:super_admin');
    Route::resource('contact-lists', ContactListController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('role:super_admin,admin,operator');
    Route::post('/contact-lists/{contactList}/contacts', [ContactListController::class, 'attachContact'])->middleware('role:super_admin,admin,operator')->name('contact-lists.contacts.attach');
    Route::delete('/contact-lists/{contactList}/contacts/{contact}', [ContactListController::class, 'detachContact'])->middleware('role:super_admin,admin,operator')->name('contact-lists.contacts.detach');
    Route::view('/segments', 'placeholder', ['title' => 'Segments'])->name('segments.index');
    Route::get('/webhook-logs', [WebhookLogController::class, 'index'])->middleware('role:super_admin,admin,viewer')->name('webhook-logs.index');
    Route::get('/import-history', [ImportHistoryController::class, 'index'])->middleware('role:super_admin,admin,operator,viewer')->name('import-history.index');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('role:super_admin,admin')->name('audit-logs.index');
    Route::get('/settings', [SettingsController::class, 'index'])->middleware('role:super_admin,admin')->name('settings.index');
});

Route::get('/unsubscribe/{token}', [UnsubscribeController::class, 'show'])->name('unsubscribe.show');
Route::post('/unsubscribe/{token}', [UnsubscribeController::class, 'store'])->name('unsubscribe.store');
Route::post('/webhooks/email/{provider}', [WebhookController::class, 'store'])->name('webhooks.email');
