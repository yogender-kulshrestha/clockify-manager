<?php
/*
|--------------------------------------------------------------------------
| Global Helper
|--------------------------------------------------------------------------
|
| This helper handles leave/timecard calculation for the application.
| The helper mainly used for calculation part of leave/timecard.
|
*/

use App\Models\Record;
use App\Models\TimeCard;
use App\Models\TimeSheet;
use Carbon\CarbonInterval;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Approver;
use App\Models\Leave;
use App\Models\Setting;
use App\Mail\CommonMail;
use App\Models\Holiday;
use App\Models\EmailAlert;
use Illuminate\Support\Facades\Http;

/**
 * calculation of total working hours according to project
 */
function total_hours($user_id, $project_id, $date_from, $date_to)
{
    $sum = TimeSheet::where('user_id', $user_id)->where('project_id', $project_id)
        ->where(function ($q) use($date_from, $date_to){
            $q->where('start_time', '>=', $date_from)->where('end_time', '<=', $date_to);
        })->sum(DB::raw("TIME_TO_SEC(duration_time)"));
    return CarbonInterval::seconds($sum)->cascade()->format('%H:%I:%S');//->forHumans();
}

/**
 * calculation of total working hours between to dates
 */
function total_working_hours($user_id, $date_from, $date_to)
{
    $sum = TimeSheet::where('user_id', $user_id)->where(function ($q) use($date_from, $date_to){
            $q->where('start_time', '>=', $date_from)->where('end_time', '<=', $date_to);
        })->sum(DB::raw("TIME_TO_SEC(duration_time)"));
    return CarbonInterval::seconds($sum)->cascade()->format('%H');//->forHumans();
}

/**
 * calculation of total no. of leaves between to dates
 */
function leave_count($user_id,$startDate,$endDate,$type=null,$leave_type_id=null,$status=null){
    $query = DB::raw("*, (CASE WHEN (date_from >= '$startDate' AND date_to <= '$endDate') THEN datediff(date_to, date_from)+1
    WHEN (date_from <= '$startDate' AND date_to >= '$endDate') THEN datediff('$endDate', '$startDate')+1
    WHEN (date_from >= '$startDate' AND date_from <= '$endDate') THEN datediff('$endDate', date_from)+1
    WHEN (date_to >= '$startDate' AND date_to <= '$endDate') THEN datediff(date_to, '$startDate')+1
    ELSE '1' END) as leave_days");
    $leaves = Leave::where('user_id', $user_id)->select($query)->where(function ($q) use($startDate, $endDate) {
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
    if($type) {
        $leaves->where('id', '!=', $type); //by other leave type
    }
    if($leave_type_id) {
        $leaves->where('leave_type_id', $leave_type_id); //by leave type
    }
    if($status == 'status') {
        $leaves->whereNotIn('status', ['Cancelled','Rejected']);
    } else {
        $leaves->where('status', 'Final Approved'); //final approved leave
    }
    $rows=$leaves->get();
    $leave=0;
    foreach($rows as $row){
        $leave+=$row->leave_days;
    }
    return $leave;
}

/**
 * calculation of total hours of leaves between to dates
 */
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
        $leaves->where('status', 'Final Approved'); //final approved leaves
    } elseif($type == 'NotApproved') {
        $leaves->where('status', '!=', 'Final Approved'); //pending for final approval
    }
    $rows=$leaves->get();
    $leave=0;
    foreach($rows as $row){
        $leave+=$row->leave_days;
    }
    return $leave*setting('day_working_hours');
}

/**
 * get approver's employees
 */
function my_employees() {
    if(auth()->user()->role == 'admin' || auth()->user()->role == 'hr') { //if admin or hr
        return User::with('leaves_balances')->where('role', 'user')->latest()->get();
    } elseif(auth()->user()->role == 'user') { //if approver
        return User::with('leaves_balances')->where('role', 'user')->whereIn('id', Approver::select('user_id')->where('approver_id', auth()->user()->id)->get())->latest()->get();
    } else {
        return [];
    }
}

/**
 * leave description
 */
function leave_description($leave_id) {
    $leave = Leave::find($leave_id);
    if($leave) {
        if($leave->user_id == auth()->user()->clockify_id) { //if your leave
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('M d Y') : Carbon::parse($leave->date_from)->format('M d').'-'.Carbon::parse($leave->date_to)->format('M d Y');
            return 'Leave Request '.$date;
        } else { //if leave for approval
            $user = $leave->user->name;
            $date = ($leave->date_from == $leave->date_to) ? Carbon::parse($leave->date_from)->format('M d Y') : Carbon::parse($leave->date_from)->format('M d').'-'.Carbon::parse($leave->date_to)->format('M d Y');
            return '['.$user.'] Leave Request '.$date;
        }
    }
}

/**
 * timecard description
 */
function timecard_description($week, $user_id, $user_name) {
    $seletedWeek = explode('-',Str::replace('W','',$week));
    $date = Carbon::now();
    $date->setISODate($seletedWeek[0],$seletedWeek[1]);
    if($date->startOfWeek()->format('Y') == $date->endOfWeek()->format('Y')) {
        $data = $date->startOfWeek()->format('M d').' - '.$date->endOfWeek()->format('M d Y');
    } else {
        $data = $date->startOfWeek()->format('M d Y').' - '.$date->endOfWeek()->format('M d Y');
    }
    if($user_id == auth()->user()->clockify_id) { //if your timecard
        return 'Week of '.$data;
    } else { //or if timecard for approval
        return '['.$user_name.'] Week of '.$data;
    }
}

/**
 * working hours error testing
 */
function verify_working_hours($week, $start, $end, $user_id) {
    $seletedWeek = explode('-',Str::replace('W','',$week));
    $date = Carbon::now();
    $startDate=Carbon::parse($start)->format('Y-m-d H:i:s');
    $endDate=Carbon::parse($end)->format('Y-m-d H:i:s');
    /** get total time entries for verification */
    $rows = TimeSheet::query()->where('start_time', '>=', $startDate)
        ->where('start_time', '<=', $endDate)
        ->where('user_id', $user_id)->orderBy('start_time')->get();
    /** start time entries testing */
    foreach ($rows as $row) {
        /** start overlay entry testing */
        //error_eo
        $overlap = TimeSheet::select('id')->where(function ($q) use ($row) {
            $q->where(function ($q) use ($row) {
                $q->where('start_time', '<=', $row->start_time)
                    ->where('end_time', '>=', $row->start_time);
            })->orWhere(function ($q) use ($row) {
                $q->where('start_time', '<=', $row->end_time)
                    ->where('end_time', '>=', $row->end_time);
            })->orWhere(function ($q) use ($row) {
                $q->where('start_time', '>=', $row->start_time)->where('end_time', '<=', $row->end_time);
            });
        })->where('user_id', $user_id)->get();

        if ($overlap->count() > 1) {
            TimeSheet::whereIn('id', $overlap)
                ->where('user_id', $user_id)->update(['error_eo' => 'Entry Overlap.']);
        } else {
            TimeSheet::whereIn('id', $overlap)
                ->where('user_id', $user_id)->update(['error_eo' => NULL]);
        }
        /** end overlay entry testing */

        /** start ot hours testing */
        //error_ot
        $start = Carbon::parse($row->start_time);
        $time = $start->startOfDay()->format('Y-m-d');
        $startTime = Carbon::parse($time . setting('working_time_from'))->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($time . setting('working_time_to'))->format('Y-m-d H:i:s');
        $h_hours = 0;
        $h_diff = Carbon::parse($endDate)->diffInDays($startDate);
        if ($h_diff > setting('day_working_hours') || $startTime > $row->start_time || $endTime < $row->end_time) {
            if ($h_diff > setting('day_working_hours')) {
                $h_hours = Carbon::parse($endDate)->diffInSeconds(Carbon::parse($startDate)->addDays(setting('day_working_hours')));
            } else {
                if ($startTime > $row->start_time) {
                    if ($startTime > $row->end_time) {
                        $h_hours += Carbon::parse($row->end_time)->diffInSeconds($row->start_time);
                    } else {
                        $h_hours += Carbon::parse($startTime)->diffInSeconds($row->start_time);
                    }
                }
                if ($endTime < $row->end_time) {
                    if ($startTime < $row->end_time) {
                        $h_hours += Carbon::parse($row->end_time)->diffInSeconds($endTime);
                    } elseif ($startTime < $row->start_time) {
                        $h_hours += Carbon::parse($row->end_time)->diffInSeconds($startTime);
                    } else {
                        $h_hours += Carbon::parse($row->end_time)->diffInSeconds($endTime);
                    }
                }
            }
            $dt = Carbon::now();
            $d_hours = $dt->diffInHours($dt->copy()->addSeconds($h_hours));
            $d_minutes = $dt->diffInMinutes($dt->copy()->subHours($d_hours)->addSeconds($h_hours));
            $message = NULL;
            if ($d_hours > 0 || $d_minutes > 0) {
                $message = 'OT:';
                if ($d_hours > 0) {
                    $message .= ' ' . $d_hours . ' hours';
                }
                if ($d_minutes > 0) {
                    $message .= ' ' . $d_minutes . ' minutes';
                }
            }
            TimeSheet::where('id', $row->id)->update(['error_ot' => $message]);
        } else {
            TimeSheet::where('id', $row->id)->update(['error_ot' => NULL]);
        }
        /** end ot hours testing */

        /** start break is missing testing */
        //error_bm
        $hours = Carbon::parse($row->start_time)->diffInHours($row->end_time);
        $minutes = Carbon::parse($row->start_time)->addHour($hours)->diffInMinutes($row->end_time);
        if($hours > 6 || ($hours == 6 && $minutes > 0)) {
            TimeSheet::where('id', $row->id)->update(['error_bm'=>'Break is missing.']);
        } else {
            TimeSheet::where('id', $row->id)->update(['error_bm'=>NULL]);
        }
        /** end break is missing testing */

        /** start overclocking hours testing */
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
        $working_hours = (setting('overclocking_hours')*60)*60;
        if($net_hours > $working_hours) {
            $ot_hours = $net_hours-$working_hours;
            TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('user_id', $user_id)->update(['error_wh'=>setting('overclocking_hours').'+ working hours.']);
        } else {
            TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('user_id', $user_id)->update(['error_wh'=>NULL]);
        }
        /** end overclocking hours testing */

        /** start long entries testing */
        //error_le
        $start_time = Carbon::parse($row->start_time)->format('Y-m-d');
        $end_time = Carbon::parse($row->end_time)->format('Y-m-d');
        $days = Carbon::parse($start_time)->diffInDays($end_time);
        if($days >= 1) {
            TimeSheet::where('id', $row->id)->update(['error_le'=>'Long entry.']);
        } else {
            TimeSheet::where('id', $row->id)->update(['error_le'=>NULL]);
        }
        /** end long entries testing */
    }
    /** start time entries testing */

    /** start storing testing errors to db */
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
    /** end storing testing errors to db */
}

/**
 * send leave & timecard mails
 */
function sendMail($type, $data)
{
    $types = ['leaveSubmit', 'leaveResubmit', 'timesheetSubmit', 'timesheetResubmit', 'leaveCancelled']; //mail type for employee
    $types_approver = ['leaveRevise', 'leaveApproved', 'leaveFinalApproved', 'timesheetRevise', 'timesheetApproved', 'leaveRejected']; //mail type for approver
    /** start employee mail section */
    if(in_array($type, $types)) {
        /** start send mail to approver/hr section */
        $owner = User::where('clockify_id', $data->user_id)->where('status', 'active')->first();
        $approver = Approver::select('approver_id')->where('user_id', $owner->clockify_id)->first();
        $user = User::where('role', 'user')->where('clockify_id', $approver->approver_id ?? '')->where('status', 'active')->first();
        if($owner && $user) {
            $email = $user->email;
            $name = $user->name;
            if ($type == 'leaveSubmit' && email_alerts('leaveSubmitToApprover') == 1) { //leave request submit mail
                $subject = 'Leave Request Submitted';
                $title = '';
                $body = 'Leave request submitted by '.$owner->name.'.';
            } elseif ($type == 'leaveResubmit' && email_alerts('leaveResubmitToApprover') == 1) { //leave resubmit mail
                $subject = 'Leave Request Re-Submitted';
                $title = '';
                $body = 'Leave request re-submitted by '.$owner->name.'.';
            } elseif ($type == 'timesheetSubmit' && email_alerts('timesheetSubmitToApprover') == 1) { //timecard submit mail
                $subject = 'Timecard Request Submitted';
                $title = '';
                $body = 'Timecard submitted by '.$owner->name.'.';
            } elseif ($type == 'timesheetResubmit' && email_alerts('timesheetResubmitToApprover') == 1) { //timecard resubmit mail
                $subject = 'Timecard Re-Submitted';
                $title = '';
                $body = 'Timecard re-submitted by '.$owner->name.'.';
            } elseif ($type == 'leaveCancelled' && email_alerts('leaveCancelledToApprover') == 1) { //leave cancel mail
                $subject = 'Leave Request Cancelled';
                $title = '';
                $body = 'Leave request cancelled by '.$owner->name.'.';
            }
            $data['to'] = $user;
            $data['owner'] = $owner;
            $data['subject'] = $subject;
            $data['title'] = $title;
            $data['body'] = $body;
            //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
            $sent = sendgridMail($data);
            /** end send mail to approver/hr section */

            /** start send mail to employee section */
            $user = $owner;
            $email = $user->email;
            $name = $user->name;
            if ($type == 'leaveSubmit' && email_alerts('leaveSubmitToEmployee') == 1) { //leave submit mail
                $subject = 'Leave Request Submitted';
                $title = '';
                $body = 'Your leave request submitted successfully.';
            } elseif ($type == 'leaveResubmit' && email_alerts('leaveResubmitToEmployee') == 1) { //leave resubmit mail
                $subject = 'Leave Request Re-Submitted';
                $title = '';
                $body = 'Your leave request re-submitted successfully.';
            } elseif ($type == 'timesheetSubmit' && email_alerts('timesheetSubmitToEmployee') == 1) { //timecard submit mail
                $subject = 'Timecard Request Submitted';
                $title = '';
                $body = 'Your timecard submitted successfully.';
            } elseif ($type == 'timesheetResubmit' && email_alerts('timesheetResubmitToEmployee') == 1) { //timecard resubmit mail
                $subject = 'Timecard Re-Submitted';
                $title = '';
                $body = 'Your timecard re-submitted successfully.';
            } elseif ($type == 'leaveCancelled' && email_alerts('leaveCancelledToEmployee') == 1) { //leave cancel mail
                $subject = 'Leave Request Cancelled';
                $title = '';
                $body = 'Your leave request cancelled successfully.';
            }
            $data['to'] = $user;
            $data['owner'] = $owner;
            $data['subject'] = $subject;
            $data['title'] = $title;
            $data['body'] = $body;
            //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
            $sent = sendgridMail($data);
            /** end send mail to employee section */
        }
    }
    /** end employee mail section */
    /** start approver/hr mail section */
    elseif(in_array($type, $types_approver)) {
        /** start sent mail to employee section */
        $user = User::where('clockify_id', $data->user_id)->where('status', 'active')->first();
        $owner = User::where('clockify_id', auth()->user()->clockify_id)->where('status', 'active')->first();
        if($user && $owner) {
            $email = $user->email;
            $name = $user->name;
            if ($type == 'leaveRevise' && email_alerts('leaveReviseToEmployee') == 1) { //leave revise and resubmit mail
                $subject = 'Leave Revise and Re-Submit';
                $title = '';
                $body = 'Your leave request disapproved by ' . $owner->name . ', Please revise and re-submit.';
            } elseif ($type == 'leaveApproved' && email_alerts('leaveApprovedToEmployee') == 1) { //leave approved mail
                $subject = 'Leave Approved';
                $title = '';
                $body = 'Your leave request Approved by ' . $owner->name . '.';
            } elseif ($type == 'leaveFinalApproved' && email_alerts('leaveFinalApprovedToEmployee') == 1) { //leave final approved mail
                $subject = 'Leave Final Approved';
                $title = '';
                $body = 'Your leave request Final Approved by ' . $owner->name . '.';
            } elseif ($type == 'timesheetRevise' && email_alerts('timesheetReviseToEmployee') == 1) { //timecard revise and resubmit mail
                $subject = 'Timecard Revise and Re-Submit';
                $title = '';
                $body = 'Your timecard disapproved by ' . $owner->name . ', Please revise and re-submit.';
            } elseif ($type == 'timesheetApproved' && email_alerts('timesheetApprovedToEmployee') == 1) { //timecard approved mail
                $subject = 'Timecard Approved';
                $title = '';
                $body = 'Your timecard Approved by ' . $owner->name . '.';
            } elseif ($type == 'leaveRejected' && email_alerts('leaveRejectedToEmployee') == 1) { //leave rejected mail
                $subject = 'Leave Rejected';
                $title = '';
                $body = 'Your leave request Rejected by ' . $owner->name . '.';
            }
            $data['to'] = $user;
            $data['owner'] = $owner;
            $data['subject'] = $subject;
            $data['title'] = $title;
            $data['body'] = $body;
            //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
            $sent = sendgridMail($data);
            /** end sent mail to employee section */

            /** start sent mail to approver/hr section */
            $owner = User::where('clockify_id', $data->user_id)->first();
            $user = User::where('clockify_id', auth()->user()->clockify_id)->first();
            $email = $user->email;
            $name = $user->name;
            if ($type == 'leaveRevise' && email_alerts('leaveReviseToApprover') == 1) { //leave revise and resubmit mail
                $subject = 'Leave Revise and Re-Submit';
                $title = '';
                $body = 'Leave request disapproved of ' . $owner->name . '.';
            } elseif ($type == 'leaveApproved' && email_alerts('leaveApprovedToApprover') == 1) { //leave approved mail
                $subject = 'Leave Approved';
                $title = '';
                $body = 'Leave request Approved of ' . $owner->name . '.';
            } elseif ($type == 'leaveFinalApproved' && email_alerts('leaveFinalApprovedToApprover') == 1) { //leave approved mail
                $subject = 'Leave Final Approved';
                $title = '';
                $body = 'Leave request Approved of ' . $owner->name . '.';
            } elseif ($type == 'timesheetRevise' && email_alerts('timesheetReviseToApprover') == 1) { //timecard revise and resubmit mail
                $subject = 'Timecard Revise and Re-Submit';
                $title = '';
                $body = 'Timecard disapproved of ' . $owner->name . '.';
            } elseif ($type == 'timesheetApproved' && email_alerts('timesheetApprovedToApprover') == 1) { //timecard approved mail
                $subject = 'Timecard Approved';
                $title = '';
                $body = 'Timecard Approved of ' . $owner->name . '.';
            } elseif ($type == 'leaveRejected' && email_alerts('leaveRejectedToApprover') == 1) { //leave rejected mail
                $subject = 'Leave Rejected';
                $title = '';
                $body = 'Leave request Rejected of ' . $owner->name . '.';
            }
            $data['to'] = $user;
            $data['owner'] = $owner;
            $data['subject'] = $subject;
            $data['title'] = $title;
            $data['body'] = $body;
            //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
            $sent = sendgridMail($data);
            /** end sent mail to approver/hr section */
        }
    }
    /** end approver/hr mail section */
    return $sent ? true : false; //check mail sent or not
}

/**
 * reminder mail for pending timecard submission to mail and for pending leave/timecard approval's to approver/hr
 */
function reminderMail($type, $data)
{
    /** start approver mail section */
    if($type == 'approver' && email_alerts('reminderToApprover') == 1) {
        $owner = User::where('clockify_id', $data->user_id)->first();
        $approver = Approver::select('approver_id')->where('user_id', $owner->clockify_id)->first();
        //$users = User::whereIn('role', ['user'])->orWhereIn('clockify_id', $approver)->get();
        //foreach($users as $user) {
        $user = User::where('clockify_id', $approver->approver_id)->firts();
            $email = $user->email;
            $name = $user->name;
            $subject = 'Reminder for approve submitted '.$data->record_type;
            $title = '';
            $body = 'Please check and approve submitted '.$data->record_type.' of '.$owner->name;
            $data['to'] = $user;
            $data['owner'] = $owner;
            $data['subject'] = $subject;
            $data['title'] = $title;
            $data['body'] = $body;
            //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
            $sent = sendgridMail($data);
        //}
    }
    /** end send mail to approver section */
    /** start send mail to employee section */
    elseif($type == 'employee' && email_alerts('reminderToEmployee') == 1) {
        $user = User::where('clockify_id', $data->clockify_id)->first();
        $owner = $user;
        $email = $user->email;
        $name = $user->name;
        $subject = 'Reminder for Timecard submittion';
        $title = '';
        $body = 'Please submit you last '.$data->weekCount.' week timecard.';
        $data['to'] = $user;
        $data['owner'] = $owner;
        $data['subject'] = $subject;
        $data['title'] = $title;
        $data['body'] = $body;
        //$sent = \Mail::to($email, $name)->send(new CommonMail($data));
        $sent = sendgridMail($data);
    }
    /** end send mail to employee section */
    return $sent ? true : false; //check mail sent or not
}

/**
 * get start date of the current year
 */
function startOfYear(){
    return Carbon::now()->startOfYear();
}

/**
 * get last date of the current year
 */
function endOfYear(){
    return Carbon::now()->endOfYear();
}

/**
 * get start date of the current month
 */
function startOfMonth(){
    return Carbon::now()->startOfMonth();
}

/**
 * get last date of the current month
 */
function endOfMonth(){
    return Carbon::now()->endOfMonth();
}

/**
 * get setting variables value
 */
function setting($value)
{
    $find = Setting::query()->first();
    return $find->$value;
}

/**
 * mail send by sendgrid
 */
function sendgridMail($details)
{
    $params = array (
        'personalizations' =>
            array (
                0 =>
                    array (
                        'to' =>
                            array (
                                0 =>
                                    array (
                                        'email' => $details->to->email,
                                    ),
                            ),
                    ),
            ),
        'from' =>
            array (
                'email' => env('MAIL_FROM_ADDRESS'),
            ),
        'subject' => $details->subject,
        'content' =>
            array (
                0 =>
                    array (
                        'type' => 'text/html',
                        'value' => '<html><head></head><body><table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;background-color:#edf2f7;margin:0;padding:0;width:100%"> <tbody> <tr> <td align="center" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif"> <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;margin:0;padding:0;width:100%"> <tbody> <tr> <td style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;padding:25px 0;text-align:center"> <a href="#" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;color:#3d4852;font-size:19px;font-weight:bold;text-decoration:none;display:inline-block" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://127.0.0.1:8000&amp;source=gmail&amp;ust=1640493248884000&amp;usg=AOvVaw2EqFQveZL2L6spVg7yTTjY"> '.config("app.name", "Clockify").' </a> </td> </tr> <tr> <td width="100%" cellpadding="0" cellspacing="0" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;background-color:#edf2f7;border-bottom:1px solid #edf2f7;border-top:1px solid #edf2f7;margin:0;padding:0;width:100%"> <table class="m_-3899946647753099578inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;background-color:#ffffff;border-color:#e8e5ef;border-radius:2px;border-width:1px;margin:0 auto;padding:0;width:570px"> <tbody> <tr> <td style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;max-width:100vw;padding:32px"> <h1 style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;color:#3d4852;font-size:18px;font-weight:bold;margin-top:0;text-align:left">Hello '.$details->to->name.'!</h1> <p style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">'.$details->body.'</p> <p style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Regards,<br> '.config("app.name", "Clockify").'</p> </td> </tr> </tbody> </table> </td> </tr> <tr> <td style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif,"> <table class="m_-3899946647753099578footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;margin:0 auto;padding:0;text-align:center;width:570px"> <tbody> <tr> <td align="center" style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;max-width:100vw;padding:32px"> <p style="box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Roboto,Helvetica,Arial,sans-serif;line-height:1.5em;margin-top:0;color:#b0adc5;font-size:12px;text-align:center">© 2022 '.config("app.name", "Clockify") .'. All rights reserved.</p> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> </tbody></table></body></html>',
                    ),
            ),
    );

    $response = Http::withHeaders([
        'Authorization' => 'Bearer '.env('MAIL_PASSWORD'),
        'Content-Type' => 'application/json',
    ])->post('https://api.sendgrid.com/v3/mail/send', $params);

    //print_r($response);

    if($response) {
        return true;
    }
    return false;
}

/**
 * Generate Employee ID
 */
function employeeId($data){
    if($data < 10) {
        $newNum = '000'.$data;
    } elseif($data >=10 || $data < 100) {
        $newNum = '00'.$data;
    } elseif($data >=100 || $data < 1000) {
        $newNum = '0'.$data;
    } else {
        $newNum = $data;
    }
    return '1PWR'.$newNum;
}

/**
 * check by date is holiday or not
 */
function is_holiday($date)
{
    return Holiday::whereDate('date', $date)->count();
}

/**
 * get holiday hours
 */
function holiday_hours($date_from,$date_to)
{
    $count = Holiday::whereDate('date', '>=', $date_from)->whereDate('date', '<=', $date_to)->count();
    return $count*setting('day_working_hours');
}

/**
 * get email alerts status
 */
function email_alerts($type)
{
    $find = EmailAlert::where('type',$type)->first();
    return $find->status ?? 0;
}

function time_entries($date, $user_id)
{
    $start_time = Carbon::parse($date)->startOfDay();
    $end_time = Carbon::parse($date)->endOfDay();
    return TimeSheet::query()->where('start_time', '>=', $start_time)
        ->where('start_time', '<=', $end_time)
        ->where('user_id', $user_id)->orderBy('start_time')->get();
}

function time_entries_hour($week, $user_id)
{
    $seletedWeek = explode('-',Str::replace('W','',$week));
    $date = Carbon::now();
    $date->setISODate($seletedWeek[0],$seletedWeek[1]);
    $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
    $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

    $net_seconds = 0;
    $ot_seconds = 0;
    $leave_seconds = 0;
    $holiday_seconds = 0;
    $unpaid_seconds = 0;
    $short_seconds = 0;

    $dt = Carbon::now();
    $rows = TimeCard::where('user_id', $user_id)->where('week', $week)->get();
    foreach ($rows as $key=>$row) {
        $is_holiday = is_holiday($row->date);
        $is_leave = leave_count($row->user_id, $row->date, $row->date, null, null, null);

        if($is_holiday > 0 || $is_leave > 0) {
            $ot_seconds += $row->net_hours;
            $net_seconds += $row->net_hours;
            $day_second = $dt->diffInSeconds($dt->copy()->addHours(setting('day_working_hours')));
            if($row->net_hours < $day_second) {
                $is_seconds = $day_second-$row->net_hours;
                if($is_leave > 0) {
                    $leave_seconds += $is_seconds;
                } elseif($is_holiday > 0) {
                    $holiday_seconds += $is_seconds;
                }
            }
        } else {
            $ot_seconds += $row->ot_hours;
            $net_seconds += $row->net_hours;
        }

        $short_seconds += $row->short_hours;
        $unpaid_seconds += $row->unpaid_hours;
    }

    $t_days = $dt->diffInHours($dt->copy()->addSeconds($net_seconds)->addSeconds($holiday_seconds)->addSeconds($leave_seconds));
    $th = $dt->diffInMinutes($dt->copy()->subHours($t_days)->addSeconds($net_seconds)->addSeconds($holiday_seconds)->addSeconds($leave_seconds));
    $td = $t_days+minutes_to_float_hours($th);
    $n_days = $dt->diffInHours($dt->copy()->addSeconds($net_seconds));
    $nh = $dt->diffInMinutes($dt->copy()->subHours($n_days)->addSeconds($net_seconds));
    $nd = $n_days+minutes_to_float_hours($nh);
    $o_days = $dt->diffInHours($dt->copy()->addSeconds($ot_seconds));
    $oh = $dt->diffInMinutes($dt->copy()->subHours($o_days)->addSeconds($ot_seconds));
    $od = $o_days+minutes_to_float_hours($oh);
    $h_days = $dt->diffInHours($dt->copy()->addSeconds($holiday_seconds));
    $hh = $dt->diffInMinutes($dt->copy()->subHours($h_days)->addSeconds($holiday_seconds));
    $hd = $h_days+minutes_to_float_hours($hh);
    $s_days = $dt->diffInHours($dt->copy()->addSeconds($short_seconds));
    $sh = $dt->diffInMinutes($dt->copy()->subHours($s_days)->addSeconds($short_seconds));
    $sd = $s_days+minutes_to_float_hours($sh);
    $u_days = $dt->diffInHours($dt->copy()->addSeconds($unpaid_seconds));
    $uh = $dt->diffInMinutes($dt->copy()->subHours($u_days)->addSeconds($unpaid_seconds));
    $ud = $u_days+minutes_to_float_hours($uh);
    $l_days = $dt->diffInHours($dt->copy()->addSeconds($leave_seconds));
    $lh = $dt->diffInMinutes($dt->copy()->subHours($l_days)->addSeconds($leave_seconds));
    $ld = $l_days+minutes_to_float_hours($lh);
    $nl_days = $dt->diffInHours($dt->copy()->addHours(leave_hours($user_id, $startDate, $endDate, 'NotApproved')));
    $nlh = $dt->diffInMinutes($dt->copy()->subHours($nl_days)->addHours(leave_hours($user_id, $startDate, $endDate, 'NotApproved')));
    $nld = $nl_days+minutes_to_float_hours($nlh);
    $data = [
        'total_hours' => $td,
        'net_hours' => $nd,
        'ot_hours' => $od,
        'holiday_hours' => $hd,
        'short_hours' => $sd,
        'unpaid_hours' => $ud,
        'leave_hours' => $ld,
        'nleave_hours' => $nld,
    ];

    return $data;
}

function minutes_to_float_hours($minutes=0)
{
    $m=0;
    $minutes = intval($minutes);
    if($minutes >= 8 && $minutes < 23) {
        $m = 0.25;
    } elseif($minutes >= 23 && $minutes < 38) {
        $m = 0.5;
    } elseif($minutes >= 38 && $minutes < 53) {
        $m = 0.75;
    } elseif ($minutes >= 53) {
        $m = 1;
    }
    return floatval($m);
}
