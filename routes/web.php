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
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeesTimeSheetController;
use App\Http\Controllers\EmployeesTimeCardController;
use App\Http\Controllers\EmployeesRecordController;
use App\Http\Controllers\EmployeesLeaveController;
use App\Http\Controllers\AuthController;

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
Route::get('/signin', function () {
    return view('auth.login');
})->name('signin');
Route::post('signin', [AuthController::class, 'login']);

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

//Clockify Routes
Route::get('/clockify/workspaces', [ClockifyController::class, 'workspaces'])->name('clockify.workspaces');
Route::get('/clockify/users', [ClockifyController::class, 'users'])->name('clockify.users');
Route::get('/clockify/projects', [ClockifyController::class, 'projects'])->name('clockify.projects');
Route::get('/clockify/user/times', [ClockifyController::class, 'timeSheets']);

Route::get('/clockify/user', [ClockifyController::class, 'index'])->name('clockify.user');
Route::get('/clockify/user/time', [ClockifyController::class, 'timeSheet']);
Route::get('/clockify/report', [ClockifyController::class, 'report'])->name('clockify.report');
Route::get('/clockify/project', [ClockifyController::class, 'project'])->name('clockify.project');

//Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/employees/ajax', [EmployeeController::class, 'employeesAjax'])->name('employees.ajax');
Route::resource('hr-managers', HrController::class);//->only(['index', 'store', 'destroy']);
Route::resource('employees', EmployeeController::class);//->only(['index', 'store', 'destroy']);
Route::resource('approvers', ApproverController::class);
Route::resource('time-sheets', TimeSheetController::class);
Route::resource('time-cards', TimeCardController::class);
Route::resource('records', RecordController::class);
Route::resource('leaves', LeaveController::class);
Route::resource('profile', ProfileController::class);

Route::resource('employees-time-sheets', EmployeesTimeSheetController::class);
Route::resource('employees-time-cards', EmployeesTimeCardController::class);
Route::resource('employees-records', EmployeesRecordController::class);
Route::resource('employees-leaves', EmployeesLeaveController::class);

Route::name('employee.')->prefix('employee')->group(function(){
    Route::get('/home', [EmployeeController::class, 'home'])->name('home');
    Route::get('/records', [EmployeeController::class, 'records'])->name('records');

    Route::get('/request-leave', [EmployeeController::class, 'requestLeave'])->name('request-leave');
    Route::post('/request-leave', [EmployeeController::class, 'storeRequestLeave']);
    Route::get('/leave/{id}/view', [EmployeeController::class, 'viewRequestLeave'])->name('leave.view');
    Route::get('/leave/{id}/edit', [EmployeeController::class, 'editRequestLeave'])->name('leave.edit');
    Route::get('/leave/{id}/review', [EmployeeController::class, 'reviewRequestLeave'])->name('leave.review');

    Route::get('/timecard', [EmployeeController::class, 'timecard'])->name('timecard');
    Route::post('/timecard', [EmployeeController::class, 'addTimeCard']);
    Route::post('/timecard/create', [EmployeeController::class, 'createTimeCard'])->name('timecard.create');
    Route::get('/timecard/{week}/submit', [EmployeeController::class, 'forSubmitTimeCard'])->name('timecard.submit');
    Route::post('/timecard/{week}/submit', [EmployeeController::class, 'submitTimecard']);

    Route::post('/timecard/{week}/view', [EmployeeController::class, 'timecard'])->name('timecard.view');
    Route::get('/timecard/{week}/edit', [EmployeeController::class, 'timecard'])->name('timecard.edit');
    Route::get('/timecard/{week}/review', [EmployeeController::class, 'timecard'])->name('timecard.review');
});
