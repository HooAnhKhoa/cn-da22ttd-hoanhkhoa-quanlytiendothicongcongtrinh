<?php

use Illuminate\Support\Facades\Route;

// --- 1. General Controllers ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

// --- 2. Admin Controllers ---
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SiteController as AdminSiteController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\IssueController as AdminIssueController;
use App\Http\Controllers\Admin\ContractsController as AdminContractsController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\MaterialUsageController as AdminMaterialUsageController;
use App\Http\Controllers\Admin\ProgressUpdateController as AdminProgressUpdateController;
use App\Http\Controllers\Admin\ContractsapproveController as AdminContractsapproveController;

// --- 3. Client Controllers (Contractor, Engineer, Owner) ---
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\EngineerSiteController; // Controller quản lý Site cho Client
use App\Http\Controllers\Client\EngineerTaskController; // Controller quản lý Task cho Kỹ sư
use App\Http\Controllers\Client\TaskController as ClientTaskController; // Controller quản lý Task cho Client
use App\Http\Controllers\Client\ContractController as ClientContractController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Client\ProgressUpdateController as ClientProgressUpdateController;
use App\Http\Controllers\Client\OwnerReportController;
use App\Http\Controllers\Client\OwnerContractController;
use App\Http\Controllers\Client\MaterialUsageController as ClientMaterialUsageController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/request-role-change', [ProfileController::class, 'requestRoleChange'])->name('profile.request-role-change');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard');

    // Role Requests
    Route::get('/role-change-requests', [AdminController::class, 'roleChangeRequests'])->name('role-change-requests');
    Route::post('/role-change-requests/{userId}/process', [AdminController::class, 'processRoleChangeRequest'])->name('role-change-requests.process');
    
    // User Management Action
    Route::post('/users/{user}/role-requests/{requestId}/approve', [AdminUserController::class, 'approveRoleRequest'])->name('users.approve-role-request');
    Route::post('/users/{user}/role-requests/{requestId}/reject', [AdminUserController::class, 'rejectRoleRequest'])->name('users.reject-role-request');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.update-status');

    // CRUD Resources
    Route::resource('users', AdminUserController::class);
    Route::resource('projects', AdminProjectController::class);
    Route::resource('sites', AdminSiteController::class);
    Route::resource('tasks', AdminTaskController::class);
    Route::resource('contracts', AdminContractsController::class);
    Route::resource('payments', AdminPaymentsController::class);
    Route::resource('documents', AdminDocumentController::class);
    Route::resource('issues', AdminIssueController::class);
    Route::resource('materials', AdminMaterialController::class);
    Route::resource('material_usage', AdminMaterialUsageController::class);
    Route::resource('progress_updates', AdminProgressUpdateController::class);
    
    // Specific Admin Routes
    Route::get('materials/statistics', [AdminMaterialController::class, 'statistics'])->name('materials.statistics');
    Route::get('progress_updates/{id}/download/{filename}', [AdminProgressUpdateController::class, 'download'])->name('progress_updates.download');
    Route::patch('contracts/{contract}/approve', [\App\Http\Controllers\Admin\ContractController::class, 'approve'])->name('contracts_approve');
    Route::resource('contracts_approve', AdminContractsapproveController::class);
});

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES (Contractor, Engineer, Owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    // 2. PROJECTS (Dự án)
    Route::middleware(['role:contractor'])->group(function () {
        Route::get('/projects/create', [ClientProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ClientProjectController::class, 'store'])->name('projects.store');
    });

    Route::middleware(['role:contractor,engineer,owner'])->group(function () {
        Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    });

    // 3. SITES (Công trường)
    Route::middleware(['role:contractor,engineer'])->group(function () {
        Route::get('/sites/create', [EngineerSiteController::class, 'create'])->name('sites.create'); // Create phải nằm trên
        Route::post('/sites', [EngineerSiteController::class, 'store'])->name('sites.store');
        Route::get('/sites/{site}/edit', [EngineerSiteController::class, 'edit'])->name('sites.edit');
        Route::match(['put', 'patch'], '/sites/{site}', [EngineerSiteController::class, 'update'])->name('sites.update');
        Route::delete('/sites/{site}', [EngineerSiteController::class, 'destroy'])->name('sites.destroy');
    });

    Route::middleware(['role:contractor,engineer,owner'])->group(function () {
        Route::get('/sites', [EngineerSiteController::class, 'index'])->name('sites.index');
        Route::get('/sites/{site}', [EngineerSiteController::class, 'show'])->name('sites.show'); // Wildcard {site} phải nằm cuối
    });

    // 4. TASKS (Công việc)
    Route::middleware(['role:contractor,engineer'])->group(function () {
        Route::get('/tasks/create', [EngineerTaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [EngineerTaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}/edit', [EngineerTaskController::class, 'edit'])->name('tasks.edit');
        Route::match(['put', 'patch'], '/tasks/{task}', [EngineerTaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [EngineerTaskController::class, 'destroy'])->name('tasks.destroy');
    });

    Route::middleware(['role:contractor,engineer,owner'])->group(function () {
        Route::get('/tasks', [EngineerTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [EngineerTaskController::class, 'show'])->name('tasks.show');
    });

    Route::middleware(['role:contractor,engineer'])->group(function () {
        Route::resource('progress_updates', ClientProgressUpdateController::class);
        Route::get('/progress-updates/{progress_update}/edit', [ClientProgressUpdateController::class, 'edit'])->name('progress_updates.edit');
        Route::match(['put', 'patch'], '/progress-updates/{progress_update}', [ClientProgressUpdateController::class, 'update'])->name('progress_updates.update');
        Route::delete('/progress-updates/{progress_update}', [ClientProgressUpdateController::class, 'destroy'])->name('progress_updates.destroy');
    });

    // 5. Progress & Contracts & Payments
    Route::middleware(['role:contractor,owner'])->group(function () {
        Route::get('/progress-updates', [ClientProgressUpdateController::class, 'index'])->name('progress_updates.index');

        // Contracts (Create cũng phải nằm trên Show)
        Route::get('/contracts/create', [ClientContractController::class, 'create'])->name('contracts.create');
        Route::post('/contracts', [ClientContractController::class, 'store'])->name('contracts.store');
        
        Route::get('/contracts', [ClientContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}', [ClientContractController::class, 'show'])->name('contracts.show');

        // Payments
        Route::get('/payments', [ClientPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [ClientPaymentController::class, 'create'])->name('payments.create');
        Route::get('/payments/{payment}', [ClientPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/download-receipt', [ClientPaymentController::class, 'downloadReceipt'])->name('payments.download-receipt');
    });

    Route::middleware(['role:contractor,engineer'])->group(function () {
        // Create
        Route::get('/material-usage/create', [ClientMaterialUsageController::class, 'create'])->name('material_usage.create');
        Route::post('/material-usage', [ClientMaterialUsageController::class, 'store'])->name('material_usage.store');
        
        // Edit & Update
        Route::get('/material-usage/{material_usage}/edit', [ClientMaterialUsageController::class, 'edit'])->name('material_usage.edit');
        Route::match(['put', 'patch'], '/material-usage/{material_usage}', [ClientMaterialUsageController::class, 'update'])->name('material_usage.update');
        
        // Delete
        Route::delete('/material-usage/{material_usage}', [ClientMaterialUsageController::class, 'destroy'])->name('material_usage.destroy');
    });

    // 6. Owner Only Routes
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/financial-reports', [OwnerReportController::class, 'index'])->name('owner.reports.index');
        Route::post('/contracts/{contract}/approve', [OwnerContractController::class, 'approve'])->name('contracts_approve'); 
        Route::get('payments/create', [ClientPaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [ClientPaymentController::class, 'store'])->name('payments.store');
    });
});