<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClockifyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\TimeCardController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
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

//Login
Route::get('/signin', function () {
    return view('auth.login');
})->name('signin');
Route::post('signin', [AuthController::class, 'login']);

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

//mail reminder
Route::get('mail-notifications', [ClockifyController::class, 'mailNotifications']);

//Clockify Routes
Route::get('/clockify/workspaces', [ClockifyController::class, 'workspaces'])->name('clockify.workspaces');
Route::get('/clockify/users', [ClockifyController::class, 'users'])->name('clockify.users');
Route::get('/clockify/projects', [ClockifyController::class, 'projects'])->name('clockify.projects');
Route::get('/clockify/user/times', [ClockifyController::class, 'timeSheets']);

//Export time entries excel routes
Route::get('export-timecard/{user_id}/{week}', [EmployeeController::class, 'exportTimecard'])->name('export.timecard');
Route::get('export-timecard', [EmployeeController::class, 'exportTimecardByDate'])->name('export.timesheet');

//Employee routes
Route::resource('employees', EmployeeController::class);

//Leave type routes
Route::resource('leave-types', LeaveTypeController::class)->only(['index', 'store', 'destroy']);

//Admin routes
Route::middleware('admin')->prefix('admin')->group(function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
    Route::post('/settings', [HomeController::class, 'settingsPost']);

    Route::post('/delete-all-records', [HomeController::class, 'deleteAllRecords'])->name('delete.all-records');
    Route::post('/delete-user-records', [HomeController::class, 'deleteAllRecordsByUser'])->name('delete.user-records');
    Route::post('/delete-all-users', [HomeController::class, 'deleteAllUsers'])->name('delete.all-users');
    Route::post('/delete-user', [HomeController::class, 'deleteByUserId'])->name('delete.user');

    Route::resource('hr-managers', HrController::class);
    Route::resource('approvers', ApproverController::class);
    Route::get('record', [EmployeeController::class, 'records'])->name('records');

    Route::resource('time-sheets', TimeSheetController::class);
    Route::resource('time-cards', TimeCardController::class);
    Route::resource('records', RecordController::class);
    Route::resource('leaves', LeaveController::class);
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
});

//Employee & Hr routes
Route::get('/employee/ajax', [EmployeeController::class, 'employeesAjax'])->name('employees.ajax');
Route::name('employee.')->group(function(){
    Route::middleware(['employee'])->group(function() {
        Route::get('/', [EmployeeController::class, 'home'])->name('home');
        Route::get('/records', [EmployeeController::class, 'records'])->name('records');
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');

        Route::get('/request-leave', [EmployeeController::class, 'requestLeave'])->name('request-leave');

        Route::get('/timesheet', [EmployeeController::class, 'timesheet'])->name('timesheet');
        Route::post('/timecard/create', [EmployeeController::class, 'createTimeCard'])->name('timecard.create');
        Route::get('/timecard/{week}', [EmployeeController::class, 'timecard'])->name('timecard');
        Route::post('/timecard/{week}', [EmployeeController::class, 'addTimeCard']);
    });

    Route::post('profile', [ProfileController::class, 'store']);

    Route::post('/request-leave', [EmployeeController::class, 'storeRequestLeave']);
    Route::get('/leave/{id}/view', [EmployeeController::class, 'viewRequestLeave'])->name('leave.view');
    Route::get('/leave/{id}/edit', [EmployeeController::class, 'editRequestLeave'])->name('leave.edit');
    Route::get('/leave/{id}/review', [EmployeeController::class, 'reviewRequestLeave'])->name('leave.review');

    Route::get('/timecard-exception', [EmployeeController::class, 'statusTimeCard'])->name('timecard.exception');
    Route::get('/timecard-delete', [EmployeeController::class, 'deleteTimeCard'])->name('timecard.delete');
    Route::get('/timecard/{week}/submit', [EmployeeController::class, 'forSubmitTimeCard'])->name('timecard.submit');
    Route::post('/timecard/{week}/submit', [EmployeeController::class, 'submitTimecard']);

    Route::get('/timecard/{week}/view', [EmployeeController::class, 'viewTimecard'])->name('timecard.view');
    Route::get('/timecard/{week}/edit', [EmployeeController::class, 'editTimecard'])->name('timecard.edit');
    Route::get('/timecard/{week}/review', [EmployeeController::class, 'reviewTimecard'])->name('timecard.review');
    Route::post('/timecard/{week}/review', [EmployeeController::class, 'submitReviewTimecard']);
});
