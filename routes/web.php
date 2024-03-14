<?php

use App\Models\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $files=File::all();
    return view('dashboard')->with('files', $files);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('files',FileController::class);


Route::get("login/google",[LoginController::class,'redirectGoogle'])->name('login.google');
Route::get("login/google/callback",[LoginController::class,'redirectGoogleCallback']);

//  Route::post('files', [FileController::class, 'store']);
//  Route::get('files/', [FileController::class, 'show']);

require __DIR__.'/auth.php';
