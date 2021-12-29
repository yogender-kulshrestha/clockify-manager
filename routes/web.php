<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClockifyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\TimeCardController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ProfileController;

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

//Clockify Routes
Route::get('/clockify/user', [ClockifyController::class, 'index'])->name('clockify.user');
Route::get('/clockify/user/time', [ClockifyController::class, 'timeSheets']);
Route::get('/clockify/report', [ClockifyController::class, 'report'])->name('clockify.report');

//Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/employees/ajax', [EmployeeController::class, 'employeesAjax'])->name('employees.ajax');
Route::resource('hr-managers', HrController::class);//->only(['index', 'store', 'destroy']);
Route::resource('employees', EmployeeController::class);//->only(['index', 'store', 'destroy']);
Route::resource('approvers', ApproverController::class);
Route::resource('time-sheets', TimeSheetController::class);
Route::resource('time-cards', TimeCardController::class);
Route::resource('records', RecordController::class);
Route::resource('profile', ProfileController::class);



