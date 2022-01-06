<?php

use App\Models\TimeSheet;
use Carbon\CarbonInterval;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Approver;
use App\Models\Leave;

function total_hours($user_id, $project_id, $date_from, $date_to)
{
    $sum = TimeSheet::where('user_id', $user_id)->where('project_id', $project_id)
        ->where(function ($q) use($date_from, $date_to){
            $q->where('start_time', '>=', $date_from)->where('end_time', '<=', $date_to);
        })->sum(DB::raw("TIME_TO_SEC(duration_time)"));
    return CarbonInterval::seconds($sum)->cascade()->format('%H:%I:%S');//->forHumans();
    /*$value = $sum;
    $dt = Carbon::now();
    $days = $dt->diffInDays($dt->copy()->addSeconds($value));
    $hours = $dt->diffInHours($dt->copy()->addSeconds($value)->subDays($days));
    $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subDays($days)->subHours($hours));
    echo CarbonInterval::days($days)->hours($hours)->minutes($minutes)->forHumans();*/
}

function total_working_hours($user_id, $date_from, $date_to)
{
    $sum = TimeSheet::where('user_id', $user_id)->where(function ($q) use($date_from, $date_to){
            $q->where('start_time', '>=', $date_from)->where('end_time', '<=', $date_to);
        })->sum(DB::raw("TIME_TO_SEC(duration_time)"));
    return CarbonInterval::seconds($sum)->cascade()->format('%H');//->forHumans();
    /*$value = $sum;
    $dt = Carbon::now();
    $days = $dt->diffInDays($dt->copy()->addSeconds($value));
    $hours = $dt->diffInHours($dt->copy()->addSeconds($value)->subDays($days));
    $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subDays($days)->subHours($hours));
    echo CarbonInterval::days($days)->hours($hours)->minutes($minutes)->forHumans();*/
}

function total_earnings($user_id, $date_from, $date_to)
{
    return $user_id.' '.$date_from.' '.$date_to;
}

function my_employees() {
    if(auth()->user()->role == 'admin' || auth()->user()->role == 'hr') {
        return User::where('role', 'user')->latest()->get();
    } elseif(auth()->user()->role == 'user') {
        return User::where('role', 'user')->whereIn('id', Approver::select('user_id')->where('approver_id', auth()->user()->id)->get())->latest()->get();
    } else {
        return [];
    }
}

function leave_description($leave_id) {
    $leave = Leave::find($leave_id);
    if($leave) {
        if($leave->user_id == auth()->user()->clockify_id) {
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('d M Y') : Carbon::parse($leave->date_from)->format('d').'-'.Carbon::parse($leave->date_to)->format('d M Y');
            return 'Leave Request '.$date;
        } else {
            $user = $leave->user->name;
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('d M Y') : Carbon::parse($leave->date_from)->format('d').'-'.Carbon::parse($leave->date_to)->format('d M Y');
            return '['.$user.'] Leave Request '.$date;
        }
    }
}

function timecard_description($leave_id) {
    $leave = Leave::find($leave_id);
    if($leave) {
        if($leave->user_id == auth()->user()->clockify_id) {
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('d M Y') : Carbon::parse($leave->date_from)->format('d').'-'.Carbon::parse($leave->date_to)->format('d M Y');
            return 'Leave Request '.$date;
        } else {
            $user = $leave->user->name;
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('d M Y') : Carbon::parse($leave->date_from)->format('d').'-'.Carbon::parse($leave->date_to)->format('d M Y');
            return '['.$user.'] Leave Request '.$date;
        }
    }
}
