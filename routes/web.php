<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

// Import Admin Controllers
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SiteController as AdminSiteController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\ProgressUpdateController as AdminProgressUpdateController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\MaterialUsageController as AdminMaterialUsageController;
use App\Http\Controllers\Admin\ContractsController as AdminContractsController;
use App\Http\Controllers\Admin\IssueController as AdminIssueController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController; 
use App\Http\Controllers\Admin\ContractsapproveController as AdminContractsapproveController; 

// ... (Import các Admin controller khác)

// Import Client Controllers
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\ProgressController as ClientProgressController;
use App\Http\Controllers\Client\IssueController as ClientIssueController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ContractController as ClientContractController;
use App\Http\Controllers\Client\OwnerContractController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Chung cho cả Admin & Client)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [HomeController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Namespace: App\Http\Controllers\Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard');

    // Quản lý chính (Full CRUD)
    Route::resource('projects', AdminProjectController::class);
    Route::resource('sites', AdminSiteController::class);
    Route::resource('tasks', AdminTaskController::class);
    Route::resource('progress_updates', AdminProgressUpdateController::class);
    Route::resource('contracts', AdminContractsController::class);
    Route::resource('issues', AdminIssueController::class);
    Route::resource('payments', AdminPaymentsController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('documents', AdminDocumentController::class);
    Route::patch('contracts/{contract}/approve', [\App\Http\Controllers\Admin\ContractController::class, 'approve'])
         ->name('contracts_approve'); // Tên này phải khớp với tên bạn gọi ở View
    Route::resource('contracts_approve', AdminContractsapproveController::class);

    // Quản lý nguồn lực
    Route::resource('materials', AdminMaterialController::class);
    Route::resource('material_usage', AdminMaterialUsageController::class);
    
    // Custom Routes cho Admin (Ví dụ)
    Route::get('materials/statistics', [AdminMaterialController::class, 'statistics'])->name('materials.statistics');
    Route::get('progress_updates/{id}/download/{filename}', [AdminProgressUpdateController::class, 'download'])->name('progress_updates.download');
});

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES (Namespace: App\Http\Controllers\Client)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

        // Routes chung cho nhiều role - SỬA LẠI PHẦN NÀY
    Route::middleware(['role:contractor,owner,engineer'])->group(function () {
        // Sửa tên route từ 'projects.show' thành 'client.projects.show' và 'client.projects.index'
        Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
        
        Route::get('/tasks', [ClientTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [ClientTaskController::class, 'show'])->name('tasks.show');
    });

    Route::middleware(['role:contractor'])->group(function () {
        Route::get('/contracts', [ClientContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}', [ClientContractController::class, 'show'])->name('contracts.show');
        
        Route::get('/progress', [ClientProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/{progress}', [ClientProgressController::class, 'show'])->name('progress.show');
    });
    
    // Owner only routes
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/my-projects', [OwnerProjectController::class, 'index'])->name('owner.projects.index');
        Route::get('/my-projects/{project}', [OwnerProjectController::class, 'show'])->name('owner.projects.show');
        Route::get('/financial-reports', [OwnerReportController::class, 'index'])->name('owner.reports.index');
        Route::post('/contracts/{contract}/approve', [OwnerContractController::class, 'approve'])
                ->name('contracts_approve'); 
    });
    // Engineer only routes
    Route::middleware(['role:engineer'])->group(function () {
        Route::get('/my-tasks', [EngineerTaskController::class, 'index'])->name('engineer.tasks.index');
        Route::get('/my-tasks/{task}', [EngineerTaskController::class, 'show'])->name('engineer.tasks.show');
        Route::post('/progress/create', [EngineerProgressController::class, 'store'])->name('engineer.progress.store');
    });
    
    Route::middleware(['role:contractor,owner'])->group(function () {
        Route::get('/contracts', [ClientContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}', [ClientContractController::class, 'show'])->name('contracts.show');
    });
});