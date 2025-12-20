<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DelayController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\DrawingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
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

// Dashboard Routes
Route::get('/project', [ProjectController::class, 'index'])->name('project');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
Route::get('/users', [UserController::class, 'index'])->name('users');

// Resource Routes - QUAN TRỌNG: Sắp xếp theo thứ tự đúng
Route::resource('users', UserController::class);
Route::resource('projects', ProjectController::class);
Route::resource('sites', SiteController::class); // ĐÃ ĐƯỢC SỬA
Route::resource('tasks', TaskController::class);
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

// Progress updates custom routes
Route::get('/progress_updates/task/{taskId}', [ProgressUpdateController::class, 'getTaskProgressUpdates']);
Route::get('/progress_updates/{id}/chart', [ProgressUpdateController::class, 'progressChart'])->name('progress_updates.chart');
Route::get('/progress_updates/{id}/download/{filename}', [ProgressUpdateController::class, 'downloadFile'])->name('progress_updates.download');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
});

Route::get('materials/statistics', [MaterialController::class, 'statistics'])->name('materials.statistics');
Route::get('materials/api/by-type', [MaterialController::class, 'getByType'])->name('materials.by-type');

Route::get('material_usage/report', [MaterialUsageController::class, 'exportReport'])->name('material_usage.report');

// Route tạo công việc từ site
Route::get('tasks/create/{site}', [TaskController::class, 'createFromSite'])->name('tasks.create.from.site');