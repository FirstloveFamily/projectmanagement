<?php

use App\Http\Controllers\PublicProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('/projects/{slug}', [PublicProjectController::class, 'show'])->name('projects.public.show');
Route::get('/projects/{slug}/export-pdf', [PublicProjectController::class, 'exportPdf'])->name('projects.public.export-pdf');
