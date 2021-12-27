<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/clockify/user', [App\Http\Controllers\ClockifyController::class, 'index'])->name('clockify.user');
Route::get('/clockify/user/time', [App\Http\Controllers\ClockifyController::class, 'timeSheets']);
Route::get('/clockify/report', [App\Http\Controllers\ClockifyController::class, 'report'])->name('clockify.report');

//Employee Routes
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('hr-managers', \App\Http\Controllers\HrController::class);
Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
Route::resource('approvers', \App\Http\Controllers\ApproverController::class);
Route::resource('time-sheets', \App\Http\Controllers\TimeSheetController::class);
Route::resource('time-cards', \App\Http\Controllers\TimeCardController::class);
Route::resource('records', \App\Http\Controllers\RecordController::class);
Route::resource('profile', \App\Http\Controllers\ProfileController::class);



