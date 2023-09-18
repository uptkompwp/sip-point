<?php

use App\Http\Controllers\SetupController;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Makul;
use App\Models\Sesi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('setup')->group(function () {
    Route::get('/', [SetupController::class, 'index']);
    Route::post('/', [SetupController::class, 'setup']);
})->middleware('setup:SETUP_PAGE');

Route::get("/{path?}", function () {
    return view('app');
})->where("path", '^(?!api\/)[\/\w\.-]*')->middleware('setup:CHECK_SETUP');
