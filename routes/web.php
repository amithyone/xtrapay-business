<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Registration Routes
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Routes that require email verification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Business Profile Routes
    Route::resource('business-profile', BusinessProfileController::class);

    // Site Routes
    Route::resource('sites', SiteController::class);
    Route::post('/sites/{site}/activate', [SiteController::class, 'activate'])->name('sites.activate');
    Route::post('/sites/{site}/deactivate', [SiteController::class, 'deactivate'])->name('sites.deactivate');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');

    // Transaction Routes
    Route::resource('transactions', TransactionController::class);

    // Statistics Routes
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/chart-data', [StatisticsController::class, 'getChartDataAjax'])->name('statistics.chart-data');

    // Withdrawal Routes
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawal-dashboard', [WithdrawalController::class, 'withdrawalDashboard'])->name('withdrawal.dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Password Management
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // PIN Management
    Route::post('/profile/pin/create', [ProfileController::class, 'createPin'])->name('profile.pin.create');
    Route::patch('/profile/pin/update', [ProfileController::class, 'updatePin'])->name('profile.pin.update');
    
    // Legacy Password Route (for compatibility)
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // Beneficiary Routes
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->name('beneficiaries.update');
    Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');

    // Ticket Routes
    Route::resource('tickets', \App\Http\Controllers\TicketController::class);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/settings', [NotificationController::class, 'saveSettings'])->name('notifications.settings');

    // API Routes
    Route::post('/api/generate-token', [DashboardController::class, 'generateApiToken'])->name('api.generate-token');
    Route::get('/api/v1/sites', [DashboardController::class, 'getSites'])->name('api.sites');
    Route::get('/api/v1/transactions', [DashboardController::class, 'getTransactions'])->name('api.transactions');
    Route::get('/api/v1/revenue', [DashboardController::class, 'getRevenue'])->name('api.revenue');
    Route::post('/api/v1/webhook/transaction', [DashboardController::class, 'webhookTransaction'])->name('api.webhook.transaction');
    
    // Dashboard Transactions Pagination
    Route::get('/dashboard/transactions', [DashboardController::class, 'getDashboardTransactions'])->name('dashboard.transactions');
});

// Admin Routes (separate from verified routes)
Route::middleware(['auth', 'admin'])->group(function () {
    // Site Management
    Route::post('/admin/sites', [AdminController::class, 'storeSite'])->name('admin.sites.store');
    Route::get('/admin/sites/{site}', [AdminController::class, 'viewSite'])->name('admin.sites.view');
    Route::put('/admin/sites/{site}', [AdminController::class, 'updateSite'])->name('admin.sites.update');
    
    // Transaction Management
    Route::post('/admin/transactions/{transaction}/approve', [AdminController::class, 'approveTransaction'])->name('admin.transactions.approve');
    Route::post('/admin/transactions/{transaction}/reject', [AdminController::class, 'rejectTransaction'])->name('admin.transactions.reject');
    
    // Withdrawal Management
    Route::post('/admin/withdrawals/{transfer}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
    Route::post('/admin/withdrawals/{transfer}/reject', [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawals.reject');
    
    // Logs
    Route::get('/admin/logs/{transaction}', [AdminController::class, 'viewLogDetails'])->name('admin.logs.view');
});

// Information page
Route::get('/information', function () {
    return view('information');
})->name('information');

// Redirect root to information page
Route::get('/', function () {
    return redirect()->route('information');
});

// PWA Routes
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json'
    ]);
});

Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript'
    ]);
});

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/manager', [AdminController::class, 'showCodeInput'])->name('admin.code.input');
Route::post('/manager/verify-code', [AdminController::class, 'verifyCode'])->name('admin.code.verify');
Route::get('/manager/second-code', [AdminController::class, 'showSecondCode'])->name('admin.second_code');
Route::post('/manager/verify-second-code', [AdminController::class, 'verifySecondCode'])->name('admin.verify_second_code');
Route::get('/manager/admin-login', [AdminController::class, 'showAdminLogin'])->name('admin.admin_login');
Route::post('/manager/admin-login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/manager/dashboard', [AdminController::class, 'index'])->name('manager.dashboard');

// Super Admin Redirect (for easier access)
Route::get('/super-admin', function () {
    if (auth()->check() && auth()->user()->superAdmin && auth()->user()->superAdmin->is_active) {
        return redirect()->route('super-admin.dashboard');
    }
    return redirect()->route('login')->with('error', 'Please login as a super admin to access this area.');
})->name('super-admin.redirect');

// Test route to check super admin status
Route::get('/test-super-admin', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'has_super_admin' => $user->superAdmin ? true : false,
            'super_admin_active' => $user->superAdmin && $user->superAdmin->is_active ? true : false,
            'is_super_admin' => $user->isSuperAdmin()
        ]);
    }
    return response()->json(['error' => 'Not authenticated']);
})->middleware('auth');

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    
    Route::get('/businesses', [SuperAdminController::class, 'businesses'])->name('businesses.index');
    Route::get('/businesses/{business}', [SuperAdminController::class, 'showBusiness'])->name('businesses.show');
    Route::get('/businesses/{business}/balance', [SuperAdminController::class, 'showBalanceUpdate'])->name('businesses.balance.show');
    Route::post('/businesses/{business}/balance', [SuperAdminController::class, 'updateBusinessBalance'])->name('businesses.balance');
    Route::get('/businesses/{business}/edit', [SuperAdminController::class, 'editBusiness'])->name('businesses.edit');
    Route::post('/businesses/{business}/update', [SuperAdminController::class, 'updateBusiness'])->name('businesses.update');
    Route::post('/businesses/{business}/toggle-verification', [SuperAdminController::class, 'toggleVerification'])->name('businesses.toggle-verification');
    
    Route::get('/withdrawals', [SuperAdminController::class, 'withdrawals'])->name('withdrawals.index');
    Route::get('/withdrawals/{withdrawal}', [SuperAdminController::class, 'showWithdrawal'])->name('withdrawals.show');
    Route::post('/withdrawals/{withdrawal}/approve', [SuperAdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [SuperAdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
    
    Route::get('/tickets', [SuperAdminController::class, 'tickets'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [SuperAdminController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{ticket}/assign', [SuperAdminController::class, 'assignTicket'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/status', [SuperAdminController::class, 'updateTicketStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/reply', [SuperAdminController::class, 'replyToTicket'])->name('tickets.reply');
    
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports.index');
    
    // Savings Management Routes
    Route::get('/savings', [SuperAdminController::class, 'savings'])->name('savings.index');
    Route::get('/savings/{business}', [SuperAdminController::class, 'showSavings'])->name('savings.show');
    Route::post('/savings/initialize', [SuperAdminController::class, 'initializeSavings'])->name('savings.initialize');
    Route::post('/savings/update', [SuperAdminController::class, 'updateSavings'])->name('savings.update');
    Route::post('/savings/reset', [SuperAdminController::class, 'resetDailySavings'])->name('savings.reset');
    Route::post('/savings/trigger-manual-collection', [SuperAdminController::class, 'triggerManualCollection'])->name('savings.trigger-manual');
    Route::post('/savings/reset-collection-time/{business}', [SuperAdminController::class, 'resetCollectionTime'])->name('savings.reset-collection-time');
    Route::post('/savings/trigger-next-collection/{business}', [SuperAdminController::class, 'triggerNextCollection'])->name('savings.trigger-next-collection');
    Route::post('/savings/update-config', [SuperAdminController::class, 'updateSavingsConfig'])->name('savings.update-config');
});

Route::get('/test-telegram', function () {
    $user = auth()->user();
    $chatId = $user->telegram_chat_id; // Make sure this is set!
    $message = "🚀 Test notification from Xtrabusiness!";
    return app(NotificationController::class)->sendTelegramNotification($chatId, $message);
});

Route::get('/test-business-telegram', [NotificationController::class, 'testBusinessTelegram'])->middleware('auth');

// Debug route to check current Telegram settings
Route::get('/debug-telegram-settings', function () {
    $user = auth()->user();
    $business = $user->businessProfile;
    
    return response()->json([
        'user_id' => $user->id,
        'business_profile_id' => $business ? $business->id : null,
        'telegram_bot_token' => $business ? $business->telegram_bot_token : null,
        'telegram_chat_id' => $business ? $business->telegram_chat_id : null,
        'business_exists' => $business ? true : false
    ]);
})->middleware('auth');

// Test route to simulate form submission
Route::post('/test-telegram-save', function (Request $request) {
    $user = auth()->user();
    $business = $user->businessProfile;
    
    if (!$business) {
        return response()->json(['error' => 'No business profile found'], 404);
    }
    
    $data = $request->only('telegram_bot_token', 'telegram_chat_id');
    $updated = $business->update($data);
    
    return response()->json([
        'success' => $updated,
        'received_data' => $data,
        'business_id' => $business->id,
        'updated_values' => [
            'telegram_bot_token' => $business->telegram_bot_token,
            'telegram_chat_id' => $business->telegram_chat_id
        ]
    ]);
})->middleware('auth');

