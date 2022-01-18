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

function leave_hours($user_id,$startDate,$endDate){
    $leaves = Leave::where('user_id', $user_id)->where('status', 'Approved')
        ->where(function ($q) use($startDate, $endDate){
            $q->whereDate('date_from', '>=', $startDate)->whereDate('date_from', '<=', $endDate);
        })->get();
    return 0;
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
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('M d Y') : Carbon::parse($leave->date_from)->format('M d').'-'.Carbon::parse($leave->date_to)->format('d Y');
            return 'Leave Request '.$date;
        } else {
            $user = $leave->user->name;
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('M d Y') : Carbon::parse($leave->date_from)->format('M d').'-'.Carbon::parse($leave->date_to)->format('d Y');
            return '['.$user.'] Leave Request '.$date;
        }
    }
}

function timecard_description($week, $user_id, $user_name) {
    $seletedWeek = explode('-',Str::replace('W','',$week));
    $date = Carbon::now();
    $date->setISODate($seletedWeek[0],$seletedWeek[1]);
    if($date->startOfWeek()->format('Y') == $date->endOfWeek()->format('Y')) {
        $data = $date->startOfWeek()->format('M d').' - '.$date->endOfWeek()->format('M d Y');
    } else {
        $data = $date->startOfWeek()->format('M d Y').' - '.$date->endOfWeek()->format('M d Y');
    }
    if($user_id == auth()->user()->clockify_id) {
        return 'Week of '.$data;
    } else {
        return '['.$user_name.'] Week of '.$data;
    }
}

function ot_hours($id, $user_id)
{
    return '';
}

function entry_overlay($id, $user_id)
{
    return '';
}

function working_hours($id, $user_id)
{
    return '';
}

function without_break($id)
{
    $data = TimeSheet::where('clockify_id',$id)->first();
    return $data;
}

function verify_working_hours($week, $start, $end, $user_id) {
    $seletedWeek = explode('-',Str::replace('W','',$week));
    $date = Carbon::now();
    //$date->setISODate($seletedWeek[0],$seletedWeek[1]);
    $startDate=Carbon::parse($start);//$date->startOfWeek()->format('Y-m-d H:i:s');
    $endDate=Carbon::parse($end);//$date->endOfWeek()->format('Y-m-d H:i:s');
    $rows = TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)
        ->where('user_id', $user_id)->orderBy('start_time')->get();
    foreach ($rows as $row) {
        $start=Carbon::parse($row->start_time);
        $time=$start->startOfDay();
        $startTime = $time->addHour(config('clockify.start_time'));
        $endTime = $time->addHour(config('clockify.end_time')-config('clockify.start_time'));
        $h_hours = 0;
        if($startTime->lt($row->start_time) || $endTime->lt($row->end_time)) {
            if($startTime->lt($row->start_time)) {
                if($startTime->lt($row->end_time)) {
                    $startTime=$row->end_time;
                }
                $h_hours += Carbon::parse($row->start_time)->diffInSeconds($startTime);
            }
            if($endTime->lt($row->end_time)) {
                $h_hours += $endTime->diffInSeconds($row->end_time);
            }
            $dt = Carbon::now();
            $d_hours = $dt->diffInHours($dt->copy()->addSeconds($h_hours));
            $d_minutes = $dt->diffInMinutes($dt->copy()->subHours($d_hours)->addSeconds($h_hours));
            $message = 'Worked on unknown time:';
            if($d_hours > 0){
                $message .=' '.$d_hours.' hours';
            }
            if($d_minutes > 0){
                $message .=' '.$d_minutes.' minutes';
            }
            TimeSheet::find($row->id)->update(['time_error'=>'1','error_remarks'=>$message]);
        } else {
            TimeSheet::where('id', $row->id)
                ->where('time_error', '1')->update(['time_error'=>'0','error_remarks'=>NULL]);
        }

        $hours = Carbon::parse($row->start_time)->diffInHours($row->end_time);
        $minutes = Carbon::parse($row->start_time)->addHour($hours)->diffInMinutes($row->end_time);
        if($hours > 6 || ($hours == 6 && $minutes > 0)) {
            TimeSheet::find($row->id)->update(['time_error'=>'4','error_remarks'=>'Break is missing.']);
        } else {
            TimeSheet::where('id', $row->id)
                ->where('time_error', '4')->update(['time_error'=>'0','error_remarks'=>NULL]);
        }

        $start=Carbon::parse($row->start_time);
        $end=Carbon::parse($start->copy()->endOfDay()->format('Y-m-d H:i:s'));
        $sheets = TimeSheet::where('start_time', '>=', $start)
            ->where('start_time', '<=', $end)
            ->where('user_id', $user_id)->get();

        $net_hours = 0;
        foreach ($sheets as $sheet) {
            $net_hours += $sheet->duration_time ?? 0;
        }
        $ot_hours = 0;
        $working_hours = (16*60)*60;
        if($net_hours > $working_hours) {
            $ot_hours = $net_hours-$working_hours;
            TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('user_id', $user_id)->update(['time_error'=>'3','error_remarks'=>'16+ working hours.']);
        } else {
            TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('time_error', '3')
                ->where('user_id', $user_id)->update(['time_error'=>'0','error_remarks'=>NULL]);
        }

        $overlay = TimeSheet::select('id')->where(function ($q) use($row){
            $q->where(function ($q) use($row){
                $q->where('start_time', '<=', $row->start_time)
                    ->where('end_time', '>=', $row->start_time);
                })->orWhere(function ($q) use($row){
                    $q->where('start_time', '<=', $row->end_time)
                        ->where('end_time', '>=', $row->end_time);
                })->orWhere(function ($q) use($row){
                    $q->where('start_time', '>=', $row->start_time)->where('end_time', '<=', $row->end_time);
                });
            })->where('user_id', $user_id)->get();

        if($overlay->count() > 1){
            TimeSheet::whereIn('id', $overlay)
                ->where('user_id', $user_id)->update(['time_error'=>'2','error_remarks'=>'Entry Overlay.']);
        } else {
            TimeSheet::whereIn('id', $overlay)->where('time_error', '2')
                ->where('user_id', $user_id)->update(['time_error'=>'0','error_remarks'=>NULL]);
        }
    }
    $rows = TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)
        ->where('user_id', $user_id)->latest()->get();
    return $rows;
}
