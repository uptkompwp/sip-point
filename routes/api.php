<?php

use App\Http\Controllers\Api\AsistenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\KuisController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\MataKuliahController;
use App\Http\Controllers\Api\PointControlller;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SesiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('apiSecret')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('jwtauth')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('profile', [AuthController::class, 'updateProfile']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
        });
    });
    Route::middleware('jwtauth')->group(function () {
        // kelas controller
        Route::post('asisten/reset', [AsistenController::class, 'reset']);
        Route::resource('asisten', AsistenController::class);
        // kelas controller
        Route::get('kelas/all', [KelasController::class, 'all']);
        Route::post('kelas/reset', [KelasController::class, 'reset']);
        Route::resource('kelas', KelasController::class);
        // makul controlller
        Route::post('mata-kuliah/reset', [MataKuliahController::class, 'reset']);
        Route::get('mata-kuliah/all', [MataKuliahController::class, 'all']);
        Route::resource('mata-kuliah', MataKuliahController::class);
        // mahasiswa controller
        Route::post('mahasiswa/reset', [MahasiswaController::class, 'reset']);
        Route::post('mahasiswa/import', [MahasiswaController::class, 'import']);
        Route::post('mahasiswa/import-save', [MahasiswaController::class, 'import_save']);
        Route::get('mahasiswa/download-format', [MahasiswaController::class, 'download_format']);
        Route::post('mahasiswa/delete-selected', [MahasiswaController::class, 'destroy_selected']);
        Route::get('mahasiswa/all', [MahasiswaController::class, 'all']);
        Route::get('mahasiswa/{id}/history', [MahasiswaController::class, 'history']);
        Route::resource('mahasiswa', MahasiswaController::class);
        // sesi controller
        Route::post('sesi/reset', [SesiController::class, 'reset']);
        Route::post('sesi/delete-selected', [SesiController::class, 'destroy_selected']);
        Route::resource('sesi', SesiController::class);
        // kuis controller
        Route::get('kuis/{sesiId}/sesi', [KuisController::class, 'index']);
        Route::post('kuis/{sesiId}/sesi', [KuisController::class, 'store']);
        Route::post('kuis/{sesiId}/reset', [KuisController::class, 'reset']);
        Route::delete('kuis/{kuis}', [KuisController::class, 'destroy']);
        Route::get('kuis/{kuis}/edit', [KuisController::class, 'edit']);
        Route::put('kuis/{kuis}', [KuisController::class, 'update']);
        Route::post('kuis/delete-selected', [KuisController::class, 'destroy_selected']);
        // point controller
        Route::get('point/{kuisId}', [PointControlller::class, 'index']);
        Route::post('point/{kuisId}', [PointControlller::class, 'store']);
        Route::delete('point/{id}', [PointControlller::class, 'destroy']);
        Route::post('point/{id}/reset', [PointControlller::class, 'reset']);
        Route::patch('point/{id}', [PointControlller::class, 'update']);
        Route::post('point/{kuisId}/import', [PointControlller::class, 'import']);
        Route::post('point/{kuisId}/import-save', [PointControlller::class, 'import_save']);
        Route::get('point-download-format', [PointControlller::class, 'download_format']);
        // home controller
        Route::get('info', [ReportController::class, 'info']);
        Route::prefix('report')->group(function () {
            Route::get('export', [ReportController::class, 'export']);
        });
    });
});
