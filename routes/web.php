<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\SiteController as AdminSiteController;
use App\Http\Controllers\Client\SiteController as ClientSiteController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Client\TaskController as ClientTaskController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DelayController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\DrawingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\MaterialUsageController;
use App\Http\Controllers\EquipmentUsageController;
use App\Http\Controllers\ProgressUpdateController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile routes (chung cho cả admin và client)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::post('/profile/avatar', [HomeController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/update', [HomeController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [HomeController::class, 'changePassword'])->name('profile.password');
});

// ==================== ADMIN ROUTES ====================
// Thay middleware 'admin' bằng closure để test
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function() {
        if (Auth::check() && Auth::user()->user_type === 'admin') {
            return app(HomeController::class)->adminDashboard();
        }
        abort(403, 'Unauthorized');
    })->name('dashboard');
    
    // Resource Controllers từ thư mục Admin
    Route::resource('users', AdminUserController::class);
    Route::resource('projects', AdminProjectController::class);
    Route::resource('sites', AdminSiteController::class);
    Route::resource('tasks', AdminTaskController::class);
    
    // Các resource controllers khác (giữ nguyên nếu chưa tách)
    Route::resource('progress_updates', ProgressUpdateController::class);
    Route::resource('milestones', MilestoneController::class);
    Route::resource('delays', DelayController::class);
    Route::resource('materials', MaterialController::class);
    Route::resource('material_usage', MaterialUsageController::class)
        ->parameters(['material_usage' => 'materialUsage']);
    Route::resource('equipment', EquipmentController::class);
    Route::resource('equipment-usage', EquipmentUsageController::class);
    Route::resource('inspections', InspectionController::class);
    Route::resource('issues', IssueController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('drawings', DrawingController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('payments', PaymentController::class);

    // Custom routes
    Route::get('/progress_updates/task/{taskId}', [ProgressUpdateController::class, 'getTaskProgressUpdates']);
    Route::get('/progress_updates/{id}/chart', [ProgressUpdateController::class, 'progressChart'])->name('progress_updates.chart');
    Route::get('/progress_updates/{id}/download/{filename}', [ProgressUpdateController::class, 'downloadFile'])->name('progress_updates.download');

    // Material routes
    Route::get('materials/statistics', [MaterialController::class, 'statistics'])->name('materials.statistics');
    Route::get('materials/api/by-type', [MaterialController::class, 'getByType'])->name('materials.by-type');
    Route::get('material_usage/report', [MaterialUsageController::class, 'exportReport'])->name('material_usage.report');

    // Document routes
    Route::get('documents/download/{filename}', [DocumentController::class, 'download'])->name('documents.download');
    // Route tạo công việc từ site
    Route::get('tasks/create/{site}', [AdminTaskController::class, 'createFromSite'])->name('tasks.create.from.site');
});

// ==================== CLIENT ROUTES ====================
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard client
    Route::get('/dashboard', [HomeController::class, 'clientDashboard'])->name('dashboard');
    Route::get('/stats/quick', [HomeController::class, 'getClientQuickStats'])->name('stats.quick');
    
    // Resource Controllers từ thư mục Client
    Route::resource('projects', ClientProjectController::class)->only(['index', 'show']);
    Route::resource('sites', ClientSiteController::class)->only(['index', 'show']);
    Route::resource('tasks', ClientTaskController::class)->only(['index', 'show']);
    
    // Client chỉ xem được
    Route::get('/progress', [ProgressUpdateController::class, 'clientIndex'])->name('progress.index');
    Route::get('/documents', [DocumentController::class, 'clientIndex'])->name('documents.index');
    Route::get('/payments', [PaymentController::class, 'clientIndex'])->name('payments.index');
    
    // Client có thể tạo issue
    Route::resource('issues', IssueController::class)->only(['index', 'create', 'store', 'show']);
    
    // Client xem hợp đồng
    Route::get('/contracts', [ContractController::class, 'clientIndex'])->name('contracts.index');
    Route::get('/contracts/{contract}', [ContractController::class, 'clientShow'])->name('contracts.show');
});
