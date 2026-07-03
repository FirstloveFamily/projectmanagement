<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ItAuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SystemRequestController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Company;
use App\Models\SystemRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $requestSummary = [
        'total' => SystemRequest::query()->count(),
        'pending' => SystemRequest::query()->where('status', SystemRequest::STATUS_PENDING)->count(),
        'approved' => SystemRequest::query()->where('status', SystemRequest::STATUS_APPROVED)->count(),
        'rejected' => SystemRequest::query()->where('status', SystemRequest::STATUS_REJECTED)->count(),
    ];

    return view('home', [
        'requestSummary' => $requestSummary,
        'companies' => Company::query()->orderBy('name')->get(),
    ]);
})->name('home');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::put('/permissions/{user}', [PermissionController::class, 'update'])->name('permissions.update');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
Route::get('/projects/{project}/tasks/{task}', [TaskController::class, 'show'])->name('projects.tasks.show');
Route::get('/projects/{project}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('projects.tasks.edit');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');

Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
Route::get('/reports/projects', [ReportsController::class, 'projectsOverview'])->name('reports.projects');
Route::get('/reports/deadlines', [ReportsController::class, 'deadlines'])->name('reports.deadlines');
Route::get('/reports/deadlines/export', [ReportsController::class, 'deadlinesExport'])->name('reports.deadlines.export');
Route::get('/reports/deadlines/export-xlsx', [ReportsController::class, 'deadlinesExportXlsx'])->name('reports.deadlines.export-xlsx');
Route::get('/reports/yearly', [ReportsController::class, 'yearly'])->name('reports.yearly');
Route::get('/reports/yearly/{company}', [ReportsController::class, 'yearlyCompany'])->name('reports.yearly.company');
Route::get('/reports/trainings', [ReportsController::class, 'trainings'])->name('reports.trainings');
Route::get('/reports/trainings/export', [ReportsController::class, 'trainingsExport'])->name('reports.trainings.export');
Route::get('/reports/trainings/export-xlsx', [ReportsController::class, 'trainingsExportXlsx'])->name('reports.trainings.export-xlsx');
Route::get('/reports/team-yearly', [ReportsController::class, 'teamYearly'])->name('reports.team-yearly');
Route::get('/reports/team-yearly/export', [ReportsController::class, 'teamYearlyExport'])->name('reports.team-yearly.export');
Route::get('/reports/team-yearly/export-xlsx', [ReportsController::class, 'teamYearlyExportXlsx'])->name('reports.team-yearly.export-xlsx');
Route::get('/requests', [SystemRequestController::class, 'index'])->name('requests.index');
Route::get('/requests/queue', [SystemRequestController::class, 'queue'])->name('requests.queue');
Route::get('/requests/report', [SystemRequestController::class, 'report'])->name('requests.report');
Route::get('/requests/{systemRequest}', [SystemRequestController::class, 'show'])->name('requests.show');
Route::get('/requests/{systemRequest}/approve', function () {
    return redirect()->route('requests.queue');
})->name('requests.approve.redirect');
Route::post('/requests', [SystemRequestController::class, 'store'])->name('requests.store');
Route::put('/requests/{systemRequest}', [SystemRequestController::class, 'update'])->name('requests.update');
Route::delete('/requests/{systemRequest}', [SystemRequestController::class, 'destroy'])->name('requests.destroy');
Route::delete('/requests/{systemRequest}/attachments/{attachment}', [SystemRequestController::class, 'destroyAttachment'])->name('requests.attachments.destroy');
Route::post('/requests/{systemRequest}/approve', [SystemRequestController::class, 'approve'])->name('requests.approve');
Route::post('/requests/{systemRequest}/reject', [SystemRequestController::class, 'reject'])->name('requests.reject');
Route::get('/it/login', [ItAuthController::class, 'create'])->name('it.login');
Route::post('/it/login', [ItAuthController::class, 'store'])->name('it.login.store');
Route::post('/it/logout', [ItAuthController::class, 'destroy'])->name('it.logout');
Route::get('/trainings', [TrainingController::class, 'index'])->name('trainings.index');
Route::post('/trainings', [TrainingController::class, 'store'])->name('trainings.store');
Route::put('/trainings/{training}', [TrainingController::class, 'update'])->name('trainings.update');
Route::delete('/trainings/{training}', [TrainingController::class, 'destroy'])->name('trainings.destroy');
Route::get('/settings', function () {
    abort_unless(Gate::allows('manage-settings'), 403);

    return view('settings.index');
})->name('settings.index');
