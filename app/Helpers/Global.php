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

function leave_hours($user_id,$startDate,$endDate,$type=null){
    $query = DB::raw("*, (CASE WHEN (date_from >= '$startDate' AND date_to <= '$endDate') THEN datediff(date_to, date_from)+1
    WHEN (date_from <= '$startDate' AND date_to >= '$endDate') THEN datediff('$endDate', '$startDate')+1
    WHEN (date_from >= '$startDate' AND date_from <= '$endDate') THEN datediff('$endDate', date_from)+1
    WHEN (date_to >= '$startDate' AND date_to <= '$endDate') THEN datediff(date_to, '$startDate')+1
    ELSE '1' END) as leave_days");
    $leaves = Leave::where('user_id', $user_id)->select($query);
    $leaves->where(function ($q) use($startDate, $endDate) {
        $q->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('date_from', '>=', $startDate)->whereDate('date_to', '<=', $endDate);
        })->orWhere(function ($q) use ($startDate, $endDate) {
            $q->whereDate('date_from', '<=', $startDate)->whereDate('date_to', '>=', $endDate);
        })->orWhere(function ($q) use ($startDate, $endDate) {
            $q->whereDate('date_from', '>=', $startDate)->whereDate('date_from', '<=', $endDate);
        })->orWhere(function ($q) use ($startDate, $endDate) {
            $q->whereDate('date_to', '>=', $startDate)->whereDate('date_to', '<=', $endDate);
        });
    });
    if($type == 'Approved') {
        $leaves->where('status', 'Approved');
    } elseif($type == 'NotApproved') {
        $leaves->where('status', '!=', 'Approved');
    }
    $rows=$leaves->get();
    $leave=0;
    foreach($rows as $row){
        $leave+=$row->leave_days;
    }
    return $leave*10;
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
    $startDate=Carbon::parse($start)->format('Y-m-d H:i:s');//$date->startOfWeek()->format('Y-m-d H:i:s');
    $endDate=Carbon::parse($end)->format('Y-m-d H:i:s');//$date->endOfWeek()->format('Y-m-d H:i:s');
    $rows = TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)
        ->where('user_id', $user_id)->orderBy('start_time')->get();
    foreach ($rows as $row) {
        //error_eo
        $overlap = TimeSheet::select('id')->where(function ($q) use($row){
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

        if($overlap->count() > 1){
            TimeSheet::whereIn('id', $overlap)
                ->where('user_id', $user_id)->update(['error_eo'=>'Entry Overlap.']);
        } else {
            TimeSheet::whereIn('id', $overlap)
                ->where('user_id', $user_id)->update(['error_eo'=>NULL]);
        }

        //error_ot
        $start=Carbon::parse($row->start_time);
        $time=$start->startOfDay();
        $startTime = $time->addHour(config('clockify.start_time'))->format('Y-m-d H:i:s');
        $endTime = $time->addHour(config('clockify.end_time')-config('clockify.start_time'))->format('Y-m-d H:i:s');
        $h_hours = 0;
        if($startTime > $row->start_time || $endTime < $row->end_time) {
            if($startTime > $row->start_time) {
                if($startTime > $row->end_time) {
                    $h_hours += Carbon::parse($row->end_time)->diffInSeconds($row->start_time);
                } else {
                    $h_hours += Carbon::parse($startTime)->diffInSeconds($row->start_time);
                }
            }
            if($endTime < $row->end_time) {
                $h_hours += Carbon::parse($endTime)->diffInSeconds($row->end_time);
            }
            $dt = Carbon::now();
            $d_hours = $dt->diffInHours($dt->copy()->addSeconds($h_hours));
            $d_minutes = $dt->diffInMinutes($dt->copy()->subHours($d_hours)->addSeconds($h_hours));
            $message = 'OT:';
            if($d_hours > 0){
                $message .=' '.$d_hours.' hours';
            }
            if($d_minutes > 0){
                $message .=' '.$d_minutes.' minutes';
            }
            TimeSheet::find($row->id)->update(['error_ot'=>$message]);
        } else {
            TimeSheet::where('id', $row->id)
                ->where('time_error', '1')->update(['error_ot'=>NULL]);
        }

        //error_bm
        $hours = Carbon::parse($row->start_time)->diffInHours($row->end_time);
        $minutes = Carbon::parse($row->start_time)->addHour($hours)->diffInMinutes($row->end_time);
        if($hours > 6 || ($hours == 6 && $minutes > 0)) {
            TimeSheet::find($row->id)->update(['error_bm'=>'Break is missing.']);
        } else {
            TimeSheet::where('id', $row->id)
                ->where('time_error', '4')->update(['error_bm'=>NULL]);
        }

        //error_wh
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
                ->where('user_id', $user_id)->update(['error_wh'=>'16+ working hours.']);
        } else {
            TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('time_error', '3')
                ->where('user_id', $user_id)->update(['error_wh'=>NULL]);
        }

        //error_le
        $days = Carbon::parse($row->start_time)->diffInDays($row->end_time);
        if($days >= 1) {
            TimeSheet::find($row->id)->update(['error_le'=>'Long entry.']);
        } else {
            TimeSheet::where('id', $row->id)
                ->where('time_error', '5')->update(['error_le'=>NULL]);
        }
    }
    TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)->where('user_id', $user_id)
        ->where(function ($q){
            $q->whereNull('error_eo')->whereNull('error_ot')->whereNull('error_bm')->whereNull('error_wh')->whereNull('error_le');
        })->update(['time_error' => '0']);
    TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)->where('user_id', $user_id)
        ->where(function ($q){
            $q->whereNotNull('error_eo')->orWhereNotNull('error_ot')->orWhereNotNull('error_bm')
                ->orWhereNotNull('error_wh')->orWhereNotNull('error_le');
        })->update(['time_error' => '1']);
    $rows = TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)
        ->where('user_id', $user_id)->latest()->get();
    return $rows;
}
