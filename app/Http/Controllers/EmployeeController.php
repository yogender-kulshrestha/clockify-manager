<?php

namespace App\Http\Controllers;

use App\Exports\TimecardExport;
use App\Models\Approver;
use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Record;
use App\Models\TimeCard;
use App\Models\TimeSheet;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;
use Str;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $now = Carbon::now()->subWeek();
        $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
        $currentWeek = $now->year.'-W'.$weekOfYear;
        $date = Carbon::now();
        $date->setISODate($now->year,$weekOfYear);
        $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
        $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

        /*$fromDate = Carbon::now()->subWeeks(5)->startOfWeek();
        $time_weeks=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)->where('record_type', 'timecard')->groupBy('description')->get();
        $date2 = Carbon::now();
        $weekOfYear2=($date2->weekOfYear < 10) ? '0'.$date2->weekOfYear : $date2->weekOfYear;
        $currentWeek2 = $date2->year.'-W'.$weekOfYear2;
        $all_weeks=TimeSheet::select('week')
            ->where('user_id',auth()->user()->clockify_id)
            ->where('week', '!=', $currentWeek2)
            //->where('start_time', '>=', $fromDate)
            ->whereNotIn('week', $time_weeks)
            ->whereNotNull('week')
            ->where('week', '!=', '')
            ->where('week', '!=', ' ')
            ->groupBy('week')->limit(5)->orderByDesc('start_time')->get();*/
        $week=[];
        $now = Carbon::now();
        for($i=0;$i<5;$i++) {
            $now->subWeek();
            $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year.'-W'.$weekOfYear;
            $week[$i] = $currentWeek;
        }
        $times=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)
            ->whereIn('description', $week)
            ->where('record_type', 'timecard')->groupBy('description')->get();
        $time_weeks=[];
        foreach($times as $k=>$time){
            $time_weeks[$k] = $time->week;
        }
        $all_weeks=[];
        $allweeks=array_diff($week,$time_weeks);
        foreach ($allweeks as $k=>$w) {
            $all_weeks[]['week'] = $w;
        }
        $weekCount=count($all_weeks);
        if($weekCount == 1) {
            $currentWeek = $all_weeks[0]['week'];
        }
        return view('employee.home', compact('weekCount','currentWeek', 'startDate', 'endDate'));
    }

    public function records(Request $request)
    {
        if($request->ajax()) {
            if($request->user_id) {
                $data = Record::where('user_id', $request->user_id)->orderByDesc('updated_at')->get();
            } else {
                if (auth()->user()->role == 'admin') {
                    $approving = User::select('clockify_id')->whereIn('role', ['user'])->get();
                } elseif (auth()->user()->role == 'hr') {
                    $approving = User::select('clockify_id')->whereIn('role', ['user'])->get();
                } else {
                    $approving = Approver::select('user_id')->where('approver_id', auth()->user()->clockify_id)->get();
                }
                $data = Record::where('user_id', auth()->user()->clockify_id)->orWhereIn('user_id', $approving)->orderByDesc('updated_at')->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query) {
                    if($query->user_id == auth()->user()->clockify_id) {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Revise and Resubmit'){
                                $action='<a href="'.route('employee.leave.edit',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Revise and Resubmit' || $query->status == 'Edit Later'){
                                $action='<a href="'.route('employee.timecard.edit',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    } else {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Submitted' || $query->status == 'Resubmitted'){
                                $action='<a href="'.route('employee.leave.review',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Submitted' || $query->status == 'Resubmitted'){
                                $action='<a href="'.route('employee.timecard.review',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    }
                    return $action;
                })->editColumn('status', function ($query) {
                    /*if($query->status == 'active'){
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';*/
                    return '<span>'.$query->status.'</span>';
                })->editColumn('record_type', function ($query) {
                    if($query->user_id == auth()->user()->clockify_id) {
                        return ucfirst($query->record_type);
                    } else {
                        return 'Approver Request ['.ucfirst($query->record_type).']';
                    }
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d-M-Y');
                })->editColumn('updated_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->updated_at)->format('d-M-Y');
                })->editColumn('description', function ($query) {
                    if(Str::lower($query->record_type) == 'timecard'){
                        $name = $query->user->name ?? '';
                        return timecard_description($query->description, $query->user_id, $name);
                    } else {
                        return leave_description($query->description);
                    }
                })->rawColumns(['record_type','status','action','created_at','updated_at'])
                ->make(true);
        }
        if(auth()->user()->role == 'admin'){
            $users = User::whereIn('role', ['user', 'hr'])->get();
        } elseif(auth()->user()->role == 'hr'){
            $users = User::whereIn('role', ['user'])->get();
        } else {
            $us = Approver::select('user_id')->where('approver_id', auth()->user()->clockify_id)->get();
            $users = User::whereIn('clockify_id', $us)->get();
        }
        return view('employee.records', compact('users'));
    }

    public function requestLeave(Request $request)
    {
        $leave_categories = LeaveType::all();
        $total_leave = LeaveBalance::where('user_id', auth()->user()->clockify_id)->sum('balance');
        $applied_leave = leave_count(auth()->user()->clokify_id, startOfYear(), endOfYear());
        return view('employee.leave', compact('leave_categories', 'total_leave', 'applied_leave'));
    }

    public function storeRequestLeave(Request $request)
    {
        try {
            $rules = [
                'leave_type_id' => 'required_if:id,null|exists:leave_types,id',
                'date_from' => 'required_if:id,null',
                'date_to' => 'required_if:id,null|after_or_equal:date_from',
                'status' => 'required|in:Submitted,Revise and Resubmit,Approved,Final Approved',
                'user_id' => 'required'
            ];
            $messages = [
                'leave_type_id.required' => 'The leave type field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'type' => '1', 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }

            if($request->user_id && $request->date_from && $request->date_to) {
                if($request->exception != '1') {
                    if($request->id){
                        $leave_hours = leave_count($request->user_id, $request->date_from, $request->date_to, $request->id, null, 'status');
                    } else {
                        $leave_hours = leave_count($request->user_id, $request->date_from, $request->date_to, null, null, 'status');
                    }
                    if($leave_hours > 0) {
                        return response()->json(['success' => false, 'type' => '1', 'message' => 'Leave request already exists between date from and date to.'], 200);
                    }

                    $total_leave = LeaveBalance::where('user_id', auth()->user()->clockify_id)->where('leave_type_id',$request->leave_type_id)->sum('balance');
                    if($request->id){
                        $year_leave = leave_count($request->user_id, startOfYear(), endOfYear(), $request->id, $request->leave_type_id);
                    } else {
                        $year_leave = leave_count($request->user_id, startOfYear(), endOfYear(), null, $request->leave_type_id);
                    }
                    $year_leave_t=$year_leave+Carbon::parse($request->date_from)->diffInDays($request->date_to)+1;
                    if($year_leave_t > $total_leave) {
                        if($year_leave >= $total_leave) {
                            return response()->json(['success' => false, 'type' => '2', 'message' => 'This leave type balance already used, Please select another one or submit with Exception.'], 200);
                        } else {
                            $le=$total_leave-$year_leave;
                            return response()->json(['success' => false, 'type' => '2', 'message' => 'This leave type balance remaining '.$le.' only, Please edit or submit with Exception'], 200);
                        }
                    }
                }
            }

            $input = $request->only('title', 'user_id', 'leave_type_id', 'date_from', 'date_to', 'remarks', 'status', 'exception');
            if($request->hasFile('attachment')) {
                $attachment = $request->attachment->store('attachments');
                $input['attachment'] = 'storage/'. $attachment;
            }
            $id = [
                'id' => $request->id,
            ];
            $insert = Leave::updateOrCreate($id, $input);
            if ($insert->wasRecentlyCreated) {
                $record = [
                    'record_type' => 'leave',
                    'user_id' => $request->user_id,
                    'status' => $request->status,
                    'description' => $insert->id,
                ];
                $data = Record::create($record);
                sendMail('leaveSubmit', $data);
                return response()->json(['success' => true, 'message' => 'Request leave create successfully.'], 200);
            } else {
                $record = [
                    'record_type' => 'leave',
                    'description' => $insert->id,
                ];
                $data = Record::where($record)->update(['status' => $request->status]);
                if($request->status == 'Approved') {
                    $status = 'leaveApproved';
                } elseif($request->status == 'Final Approved') {
                    $status = 'leaveFinalApproved';
                } elseif($request->status == 'Revise and Resubmit') {
                    $status = 'leaveRevise';
                } else {
                    $status = 'leaveResubmit';
                    Record::where($record)->update(['status' => 'Resubmitted']);
                }
                $data = Record::where($record)->first();
                sendMail($status, $data);
                return response()->json(['success' => true, 'type' => '1', 'message' => 'Request leave update successfully.'], 200);
            }
            return response()->json(['success' => false, 'type' => '1', 'message' => 'Request leave failed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'type' => '1', 'message' => $e->getMessage()], 200);
        }
    }

    public function viewRequestLeave($id)
    {
        $data = Leave::find($id);
        $leave_categories = LeaveType::all();
        if($data) {
            return view('employee.leave-view', compact('data', 'leave_categories'));
        }
        abort(404);
    }

    public function reviewRequestLeave($id)
    {
        $data = Leave::find($id);
        $leave_categories = LeaveType::all();
        if($data) {
            return view('employee.leave-review', compact('data', 'leave_categories'));
        }
        abort(404);
    }

    public function editRequestLeave($id)
    {
        $data = Leave::find($id);
        $leave_categories = LeaveType::all();
        if($data) {
            $total_leave = LeaveType::sum('balance');
            $applied_leave = leave_count(auth()->user()->clokify_id, startOfYear(), endOfYear(), $data->id);
            return view('employee.leave-edit', compact('data', 'leave_categories', 'total_leave', 'applied_leave'));
        }
        abort(404);
    }

    public function timecard($week, Request $request)
    {
        if($request->ajax()) {
            verify_working_hours($week, $request->start_time, $request->end_time, auth()->user()->clockify_id);
            $data = TimeSheet::query()->where('start_time', '>=', $request->start_time)
                ->where('start_time', '<=', $request->end_time)
                ->where('user_id', auth()->user()->clockify_id)->orderBy('start_time')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    $exception = ($query->exception == 0) ? 'Request' : 'Remove';
                    $action = '<a data-id="'.$query->id.'" data-remarks="'.$query->employee_remarks.'" data-description="'.$query->description.'" data-start_time="'.Carbon::parse($query->start_time)->format('Y-m-d\TH:i').'" data-end_time="'.Carbon::parse($query->end_time)->format('Y-m-d\TH:i').'" class="rowedit btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        Edit
                    </a>';
                    if(!empty($query->error_eo)) {
                        $action .= '<a data-id="' . $query->id . '" class="rowdelete btn btn-dark btn-sm">
                            Delete
                        </a>';
                    } else {
                        $action .= '<a data-id="' . $query->id . '" data-exception="' . $query->exception . '" data-description="' . $query->description . '" data-start_time="' . $query->start_time . '" data-end_time="' . $query->end_time . '" class="exception btn btn-dark btn-sm">
                            ' . $exception . ' Exception
                        </a>';
                    }
                    return $action;
                })->editColumn('project', function ($query) {
                    return $query->project->name ?? '';
                })->editColumn('status', function ($query) {
                    if($query->status == 'active'){
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';
                })->addColumn('start_date', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('d-M-Y');
                })->editColumn('start_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('H:i');
                })->addColumn('end_date', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('d-M-Y');
                })->editColumn('end_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('H:i');
                })->addColumn('time_duration', function ($query) {
                    return CarbonInterval::seconds($query->duration_time)->cascade()->forHumans();
                })->addColumn('error', function ($query) {
                    return $query->error_eo.'<br/>'.$query->error_ot.'<br/>'.$query->error_bm.'<br/>'.$query->error_wh.'<br/>'.$query->error_le;
                })
                ->rawColumns(['error','status','action','start_date','start_time','end_date','end_time','time_duration','created_at'])
                ->make(true);
        }
        /*$now = Carbon::now()->subWeek();
        $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
        $currentWeek = $now->year.'-W'.$weekOfYear;*/
        $currentWeek=$week;
        $seletedWeek = explode('-',Str::replace('W','',$week));
        $date = Carbon::now();
        $date->setISODate($seletedWeek[0],$seletedWeek[1]);
        $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
        $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');
        return view('employee.timecard', compact('currentWeek','startDate','endDate'));
    }

    public function addTimeCard($week, Request $request)
    {
        try {
            $rules = [
                'description' => 'nullable|max:255',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $date_from = Carbon::parse($request->start_time);
            $date_to = Carbon::parse($request->end_time);
            $diff = $date_from->diffInSeconds($date_to);
            $now = Carbon::parse($request->start_time);
            $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year.'-W'.$weekOfYear;
            if($weekOfYear == 52) {
                if($now->format('d') < 7) {
                    $currentWeek = $now->subYear()->year.'-W'.$weekOfYear;
                }
            }
            $input = $request->only( 'description', 'start_time', 'end_time', 'employee_remarks');
            $input['duration']=$diff;
            $input['duration_time']=$diff;
            $input['week']=$currentWeek;
            $input['clockify_id']=time();
            $input['billable']='0';
            $input['workspace_id']=time();
            $input['is_locked']='0';
            $input['custom_field_values']=[];
            $input['user_id']=auth()->user()->clockify_id;
            $id = [
                'id' => $request->id,
            ];
            $insert = TimeSheet::updateOrCreate($id, $input);
            if ($insert) {
                $message = $request->id ? 'Updated Successfully.' : 'Added Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);
            }
            $message = $request->id ? 'Updating Failed.' : 'Adding Failed.';
            return response()->json(['success' => false, 'message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    public function deleteTimeCard(Request $request){
        try {
            TimeSheet::where('id',$request->id)->delete();
            return response()->json(['success' => true, 'message' => 'Delete successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    public function statusTimeCard(Request $request){
        try {
            TimeSheet::where('id',$request->id)->update(['exception' => strval($request->status)]);
            $exception_text = ($request->status == '1') ? 'Requested' : 'Removed';
            return response()->json(['success' => true, 'message' => 'Exception '.$exception_text.' successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    public function createTimeCard(Request $request)
    {
        $rules = [
            'start_time' => 'required',
            'end_time' => 'required'
        ];
        $request->validate($rules);
        $count = TimeSheet::where('start_time', '>=', $request->start_time)
            ->where('start_time', '<=', $request->end_time)
            ->where('time_error', '1')->where('exception', '!=', '1')
            ->where('user_id', auth()->user()->clockify_id)->count();
        if($count > 0){
            return redirect()->back()->withError('Please fixed the all errors or Request Exception.');
        }
        $start=Carbon::parse($request->start_time);
        $end=Carbon::parse($start->copy()->endOfDay()->format('Y-m-d H:i:s'));
        $start->subDay();
        $end->subDay();
        for($i=0;$i<7;$i++){
            $start->addDay();
            $end->addDay();
            $sheets = TimeSheet::where('start_time', '>=', $start)
                ->where('start_time', '<=', $end)
                ->where('user_id', auth()->user()->clockify_id)->get();
            $flags = '';
            $description = '';
            $net_hours = 0;
            $employee_remarks = '';
            $exception = '0';
            $error_eo='';
            $error_ot='';
            $error_bm='';
            $error_wh='';
            $error_le='';
            foreach ($sheets as $sheet) {
                $error_eo .= ($error_eo == '') ? $sheet->error_eo : '';
                $error_ot .= ($error_ot == '') ? $sheet->error_ot : '';
                $error_bm .= ($error_bm == '') ? $sheet->error_bm : '';
                $error_wh .= ($error_wh == '') ? $sheet->error_wh : '';
                $error_le .= ($error_le == '') ? $sheet->error_le : '';
                $description .= $sheet->description ? $sheet->description.'<br/> ' : '';
                $net_hours += $sheet->duration_time ?? 0;
                $employee_remarks .= $sheet->employee_remarks ? $sheet->employee_remarks.'<br/> ' : '';
                if($sheet->exception == '1') {
                    $exception = '1';
                }
            }
            $flags .= $error_eo ? $error_eo.'<br/>' : '';
            $flags .= $error_ot ? $error_ot.'<br/>' : '';
            $flags .= $error_bm ? $error_bm.'<br/>' : '';
            $flags .= $error_wh ? $error_wh.'<br/>' : '';
            $flags .= $error_le ? $error_le.'<br/>' : '';
            $now = Carbon::parse($start);
            $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year.'-W'.$weekOfYear;
            if($weekOfYear == 52) {
                if($now->format('d') < 7) {
                    $currentWeek = $now->subYear()->year.'-W'.$weekOfYear;
                }
            }
            $ot_hours = 0;
            $working_hours = (10*60)*60;
            if($net_hours > $working_hours) {
                $ot_hours = $net_hours-$working_hours;
            }
            $id = [
                'user_id' => auth()->user()->clockify_id,
                'date' => $start->format('Y-m-d'),
            ];
            $input = [
                'exception' => $exception,
                'week' => $currentWeek,
                'flags' => $flags,
                'description' => $description,
                'ot_hours' => $ot_hours,
                'net_hours' => $net_hours,
                'employee_remarks' => $employee_remarks
            ];
            $insert = TimeCard::updateOrCreate($id, $input);
            /*$dt = Carbon::now();
            $hours = $dt->diffInHours($dt->copy()->addSeconds($net_hours));
            $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($net_hours)->subHours($hours));
            if($minutes <= 15) {
            } elseif ($minutes > 15 || $minutes <= 30) {
            } elseif ($minutes > 30 || $minutes <= 45) {
            } else {
            }*/
        }
        if($insert) {
            return redirect()->to(route('employee.timecard.submit',['week' => $currentWeek]))->withSuccess('Time card create successfully.');
        }
        return redirect()->back()->withError('Timecard not create.');
    }

    public function forSubmitTimeCard($week)
    {
        $user_id=auth()->user()->clockify_id;
        $rows=TimeCard::where('user_id', $user_id)->where('week', $week)->get();
        if($rows !== null) {
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hour = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            $leave_hours = leave_hours($user_id, $startDate, $endDate, 'Approved');
            $nleave_hours = leave_hours($user_id, $startDate, $endDate, 'NotApproved');
            $net_hours = $net_hour+$leave_hours;
            $leave_categories = LeaveType::all();
            return view('employee.timecard-submit', compact('leave_categories','week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours','leave_hours','nleave_hours'));
        }
        return redirect()->back()->withError('Please create timecard first.');
    }

    public function submitTimeCard(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Submitted,Revise and Resubmit,Approved,Edit Later',
            'user_id' => 'required',
            'week' => 'required',
        ]);

        $input = $request->only('user_id', 'status');
        $id = [
            'record_type' => 'timecard',
            'user_id' => $request->user_id,
            'description' => $request->week,
        ];
        $insert = Record::updateOrCreate($id, $input);
        if ($insert->wasRecentlyCreated) {
            if($request->status == 'Submitted') {
                $status = 'timesheetSubmit';
                $insert = Record::find($insert->id);
                sendMail($status, $insert);
            }
            $message = ($request->status == 'Edit Later') ? 'Timecard save successfully.' : 'Timecard submitted successfully.';
            return redirect()->to(route('employee.records'))->withSuccess($message);
        } else {
            $insert = Record::where($id)->first();
            if($request->status == 'Approved') {
                $status = 'timesheetApproved';
                sendMail($status, $insert);
            } elseif($request->status == 'Revise and Resubmit') {
                $status = 'timesheetRevise';
                sendMail($status, $insert);
            } elseif($request->status == 'Submitted') {
                Record::where($id)->update(['status' => 'Resubmitted']);
                $status = 'timesheetResubmit';
                sendMail($status, $insert);
            }
            $message = ($request->status == 'Edit Later') ? 'Timecard save successfully.' : 'Timecard re-submitted successfully.';
            return redirect()->to(route('employee.records'))->withSuccess($message);
        }
        return redirect()->back()->withError('Timecard not submit.');
    }

    public function viewTimecard($id)
    {
        $data = Record::where('record_type', 'timecard')->where('id', $id)->first();
        if($data !== null) {
            $week=$data->description;
            $user_id=$data->user_id;
            $rows=TimeCard::where('user_id', $user_id)->where('week', $week)->get();
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hour = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            $leave_hours = leave_hours($user_id, $startDate, $endDate, 'Approved');
            $nleave_hours = leave_hours($user_id, $startDate, $endDate, 'NotApproved');
            $net_hours = $net_hour+$leave_hours;
            return view('employee.timecard-view', compact('data','week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours', 'leave_hours', 'nleave_hours'));
        }
        return redirect()->to(route('employee.records'))->withError('Please create timecard first.');
    }

    public function editTimecard($id)
    {
        $data = Record::where('record_type', 'timecard')->where('id', $id)->first();
        if($data !== null) {
            $week=$data->description;
            $currentWeek=$week;
            $user_id=$data->user_id;
            $rows=TimeCard::where('user_id', $user_id)->where('week', $week)->get();
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hour = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            $leave_hours = leave_hours($user_id, $startDate, $endDate, 'Approved');
            $nleave_hours = leave_hours($user_id, $startDate, $endDate, 'NotApproved');
            $net_hours = $net_hour+$leave_hours;
            return view('employee.timecard-edit', compact('data','week','currentWeek','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours', 'leave_hours', 'nleave_hours'));
        }
        return redirect()->to(route('employee.records'))->withError('Please create timecard first.');
    }

    public function reviewTimecard($id)
    {
        $data = Record::where('record_type', 'timecard')->where('id', $id)->first();
        if($data !== null) {
            $week=$data->description;
            $user_id=$data->user_id;
            $rows=TimeCard::where('user_id', $user_id)->where('week', $week)->get();
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hour = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            $leave_hours = leave_hours($user_id, $startDate, $endDate, 'Approved');
            $nleave_hours = leave_hours($user_id, $startDate, $endDate, 'NotApproved');
            $net_hours = $net_hour+$leave_hours;
            return view('employee.timecard-review', compact('data','week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours', 'leave_hours', 'nleave_hours'));
        }
        return redirect()->to(route('employee.records'))->withError('Please create timecard first.');
    }

    public function submitReviewTimecard($week, Request $request)
    {
        $remarks = $request->remarks;
        $data = Record::where('description', $week)->where('user_id', $request->user_id)->update(['status' => $request->status]);
        if($data) {
            foreach ($remarks as $remark) {
                TimeCard::where('id', $remark['id'])->update(['approver_remarks' => $remark['remarks']]);
            }
            $data = Record::where('description', $week)->where('user_id', $request->user_id)->first();
            if($request->status == 'Approved') {
                $status = 'timesheetApproved';
                sendMail($status, $data);
            } elseif($request->status == 'Revise and Resubmit') {
                $status = 'timesheetRevise';
                sendMail($status, $data);
            }
            return redirect()->to(route('employee.records'))->withSuccess('Timecard '.$request->status.' successfully.');
        }
        return redirect()->to(route('employee.records'))->withError('Timecard '.$request->status.' failed.');
    }

    public function timesheet(Request $request)
    {
        $week=[];
        $now = Carbon::now();
        for($i=0;$i<5;$i++) {
            $now->subWeek();
            $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year.'-W'.$weekOfYear;
            $week[$i] = $currentWeek;
        }
        $times=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)
            ->whereIn('description', $week)
            ->where('record_type', 'timecard')->groupBy('description')->get();
        $time_weeks=[];
        foreach($times as $k=>$time){
            $time_weeks[$k] = $time->week;
        }
        $all_weeks=[];
        $allweeks=array_diff($week,$time_weeks);
        foreach ($allweeks as $k=>$w) {
            $all_weeks[]['week'] = $w;
        }
        $all_weeks=json_decode(json_encode($all_weeks));
        return view('employee.timesheet', compact('all_weeks'));
    }

    public function employeesAjax(Request $request)
    {
        if(request()->ajax()) {
            $sql = User::query();
            if($request->employees) {
                $employees = explode(',',$request->employees);
                $sql->whereNotIn('clockify_id', $employees);
            }
            $users=$sql->where('role', 'user')->get();
            if($users->count() > 0) {
                return response()->json(['success' => true, 'message' => 'Data found successfully.', 'data' => $users], 200);
            }
            return response()->json(['success' => false, 'message' => 'No data found.', 'data' => $users], 200);
        }
        abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = my_employees();//User::where('role', 'user')->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return "<a data-leave_balances='".$query->leave_balances."' data-id='".$query->id."' data-name='".$query->name."' data-email='".$query->email."' data-type='".$query->type."' data-status='".$query->status."' class='mx-1 rowedit' data-bs-toggle='modal' data-bs-target='#modal-create' data-bs-toggle='tooltip' data-bs-original-title='Edit'>
                        <i class='fas fa-edit text-primary'></i>
                    </a>
                    <a href='".route('employees.show',['employee'=>$query->clockify_id])."' data-bs-toggle='tooltip' data-bs-original-title='View'>
                        <i class='fas fa-eye text-success'></i>
                    </a>";
                })->editColumn('type', function ($query) {
                    return ($query->type == 'fulltime') ? 'Full Time' : ucfirst($query->type);
                })->editColumn('status', function ($query) {
                    if($query->status == 'active'){
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d M, Y');
                })
                ->rawColumns(['status','action','created_at'])
                ->make(true);
        }
        return view('employees.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|min:3|max:255',
                'email' => 'required_if:id,null|email|exists:users,email',
                'type' => 'required',
                'status' => 'required|in:active,inactive',
                'password' => 'required_without:id|confirmed'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('name', 'status', 'type');
            if($request->password) {
                $input['password']=Hash::make($request->password);
            }
            $id = [
                'id' => $request->id,
                'role' => 'user',
            ];
            $insert = User::updateOrCreate($id, $input);
            if ($insert) {
                foreach($request->leave_balances as $balance) {
                    $user = User::find($insert->id);
                    LeaveBalance::where('user_id', $user->clockify_id)
                        ->where('leave_type_id', $balance['leave_type_id'])->update(['balance' => $balance['balance']]);
                }
                $message = $request->id ? 'Updated Successfully.' : 'Added Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);
            }
            $message = $request->id ? 'Updating Failed.' : 'Adding Failed.';
            return response()->json(['success' => false, 'message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $data = Record::where('user_id', $request->user_id)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()->addColumn('action', function($query) {
                    if($query->user_id == auth()->user()->clockify_id) {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Revise and Resubmit'){
                                $action='<a href="'.route('employee.leave.edit',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Revise and Resubmit' || $query->status == 'Edit Later'){
                                $action='<a href="'.route('employee.timecard.edit',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    } else {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Submitted' || $query->status == 'Resubmitted'){
                                $action='<a href="'.route('employee.leave.review',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Submitted' || $query->status == 'Resubmitted'){
                                $action='<a href="'.route('employee.timecard.review',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    }
                    return $action;
                })->editColumn('status', function ($query) {
                    /*if($query->status == 'active'){
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';*/
                    return '<span>'.$query->status.'</span>';
                })->editColumn('record_type', function ($query) {
                    if($query->user_id == auth()->user()->clockify_id) {
                        return ucfirst($query->record_type);
                    } else {
                        return 'Approver Request ['.ucfirst($query->record_type).']';
                    }
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d-M-Y');
                })->editColumn('updated_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->updated_at)->format('d-M-Y');
                })->editColumn('description', function ($query) {
                    if(Str::lower($query->record_type) == 'timecard'){
                        $name = $query->user->name ?? '';
                        return timecard_description($query->description, $query->user_id, $name);
                    } else {
                        return leave_description($query->description);
                    }
                })->rawColumns(['record_type','status','action','created_at','updated_at'])
                ->make(true);
        }
        $data = User::where('clockify_id', $id)->first();
        if ($data) {
            $user_id=$data->clockify_id;
            $startDate = Carbon::parse('2000-01-01 00:00:00')->format('Y-m-d H:i:s');
            $dt = Carbon::now();
            $net_hour = TimeCard::where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('user_id', $user_id)->sum('unpaid_hours');
            $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            $leave_hours = leave_hours($user_id, $startDate, $dt, 'Approved');
            $nleave_hours = leave_hours($user_id, $startDate, $dt, 'NotApproved');
            $net_hours = $net_hour+$leave_hours;
            return view('employees.show', compact('data','net_hours','ot_hours','short_hours','unpaid_hours','leave_hours','nleave_hours'));
        }
        abort(404);
    }

    public function destroy($id)
    {
        $data = User::find($id)->delete();
        if($data){
            return response()->json(['success' => true, 'message' => 'Deleted Successfully.'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion Failed.'], 200);
    }

    public function exportTimecard($user_id, $week='2022-W03')
    {
        $user=User::where('clockify_id', $user_id)->first();
        $seletedWeek = explode('-',Str::replace('W','',$week));
        $date = Carbon::now();
        $date->setISODate($seletedWeek[0],$seletedWeek[1]);
        $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
        $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');
        $dt = Carbon::now();
        $net_hour = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
        $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
        $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
        $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
        $net_hour = $dt->diffInHours($dt->copy()->addSeconds($net_hour));
        $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
        $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
        $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
        $leave_hours = leave_hours($user_id, $startDate, $endDate, 'Approved');
        $nleave_hours = leave_hours($user_id, $startDate, $endDate, 'NotApproved');
        $net_hours = $net_hour+$leave_hours;

        $data = [
            [
                'Name' => $user->name,
                'Email' => $user->email,
                'Description' => 'Time Card Report of '.Carbon::parse($startDate)->format('d M, Y').' - '.Carbon::parse($endDate)->format('d M, Y').' ['.$week.']',
                'Total Hours' => $net_hours ?? 0,
                'OT Hours' => $ot_hours ?? 0,
                'Leave Hours' => $leave_hours ?? 0,
                'Unapproved Leave Hours' => $nleave_hours ?? 0,
                'Short Hours' => $short_hours ?? 0,
                'Unpaid Hours' => $unpaid_hours ?? 0,
            ]
        ];
        $timecards=TimeCard::where('user_id',$user_id)->where('week',$week)->get();
        $timecard=[];
        foreach ($timecards as $t) {
            $timecard[] = [
                'Date' => $t->date,
                'Flags' => strip_tags($t->flags),
                'OT Hours' => CarbonInterval::seconds($t->ot_hours)->cascade()->forHumans(),
                'Net Hours' => CarbonInterval::seconds($t->net_hours)->cascade()->forHumans(),
                'Employee Remarks' => strip_tags($t->employee_remarks),
                'Approver Remarks' => strip_tags($t->approver_remarks),
            ];
        }
        $timesheets=TimeSheet::where('user_id',$user_id)->where('week',$week)->get();
        $timesheet=[];
        foreach ($timesheets as $t) {
            $timesheet[] = [
                'Start Date' => Carbon::createFromFormat('Y-m-d H:i:s', $t->start_time)->format('d-M-Y'),
                'Start Time' => Carbon::createFromFormat('Y-m-d H:i:s', $t->start_time)->format('H:i'),
                'End Date' => Carbon::createFromFormat('Y-m-d H:i:s', $t->end_time)->format('d-M-Y'),
                'End Time' => Carbon::createFromFormat('Y-m-d H:i:s', $t->end_time)->format('H:i'),
                'Duration' => CarbonInterval::seconds($t->duration_time)->cascade()->forHumans(),
                'Error' => $t->error_eo.' '.$t->error_ot.' '.$t->error_bm.' '.$t->error_wh.' '.$t->error_le,
                'Employee Remarks' => strip_tags($t->employee_remarks),
                'Approver Remarks' => strip_tags($t->approver_remarks),
            ];
        }
        $arrays = [$data, $timecard, $timesheet];
        return Excel::download(new TimecardExport($arrays), $user->name.'-'.$week.'-timecard.xlsx');
    }

    public function exportTimecardByDate(Request $request)
    {
        try {
            $rules = [
                'week_from' => 'required',
                'week_to' => 'required|after_or_equal:week_from',
                'user_id' => 'required',
            ];
            $messages=[
                'week_to.after_or_equal' => 'The week to must be after or equal to week from.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $error = '';
                if (!empty($validator->errors())) {
                    $error = $validator->errors()->first();
                }
                return redirect()->back()->withInput()->withError($error);
            }
            $seletedWeek = explode('-',Str::replace('W','',$request->week_from));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            //$endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $seletedWeek2 = explode('-',Str::replace('W','',$request->week_to));
            $date2 = Carbon::now();
            $date2->setISODate($seletedWeek2[0],$seletedWeek2[1]);
            //$startDate=$date2->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date2->endOfWeek()->format('Y-m-d H:i:s');

            $user=User::where('clockify_id', $request->user_id)->first();
            if($user) {
                $data = [
                    [
                        'Name' => $user->name,
                        'Email' => $user->email,
                        'Description' => 'Time Card Report of ' . $request->week_from . ' - ' . $request->week_to,
                    ]
                ];
                $timecards = TimeCard::where('user_id', $user->clockify_id)
                    ->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate)->get();
                $timecard = [];
                if(count($timecards) > 0) {
                    foreach ($timecards as $t) {
                        $otHours = CarbonInterval::seconds($t->ot_hours)->cascade()->forHumans();
                        $otHours = ($otHours == '1 second') ? '-' : $otHours;
                        $netHours = CarbonInterval::seconds($t->net_hours)->cascade()->forHumans();
                        $netHours = ($netHours == '1 second') ? '-' : $netHours;
                        $timecard[] = [
                            'Date' => $t->date,
                            'Flags' => strip_tags($t->flags),
                            'OT Hours' => $otHours,
                            'Net Hours' => $netHours,
                            'Employee Remarks' => strip_tags($t->employee_remarks),
                            'Approver Remarks' => strip_tags($t->approver_remarks),
                        ];
                    }
                } else {
                    $timecard[] = [
                        'Date' => '',
                        'Flags' => '',
                        'OT Hours' => '',
                        'Net Hours' => '',
                        'Employee Remarks' => '',
                        'Approver Remarks' => '',
                    ];
                }
                $timesheets = TimeSheet::where('user_id', $user->clockify_id)
                    ->where('start_time', '>=', $startDate)
                    ->where('start_time', '<=', $endDate)->get();
                $timesheet = [];
                if(count($timesheets) > 0) {
                    foreach ($timesheets as $t) {
                        $timesheet[] = [
                            'Start Date' => Carbon::createFromFormat('Y-m-d H:i:s', $t->start_time)->format('d-M-Y'),
                            'Start Time' => Carbon::createFromFormat('Y-m-d H:i:s', $t->start_time)->format('H:i'),
                            'End Date' => Carbon::createFromFormat('Y-m-d H:i:s', $t->end_time)->format('d-M-Y'),
                            'End Time' => Carbon::createFromFormat('Y-m-d H:i:s', $t->end_time)->format('H:i'),
                            'Duration' => CarbonInterval::seconds($t->duration_time)->cascade()->forHumans(),
                            'Error' => $t->error_eo . ' ' . $t->error_ot . ' ' . $t->error_bm . ' ' . $t->error_wh . ' ' . $t->error_le,
                            'Employee Remarks' => strip_tags($t->employee_remarks),
                            'Approver Remarks' => strip_tags($t->approver_remarks),
                        ];
                    }
                } else {
                    $timesheet[] = [
                        'Start Date' => '',
                        'Start Time' => '',
                        'End Date' => '',
                        'End Time' => '',
                        'Duration' => '',
                        'Error' => '',
                        'Employee Remarks' => '',
                        'Approver Remarks' => '',
                    ];
                }
                $arrays = [$data, $timecard, $timesheet];
                return Excel::download(new TimecardExport($arrays), $user->name.'-'.Carbon::now().'-timecard.xlsx');
            }
            return redirect()->back()->withInput()->withError('Record Not Found.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withError('Record Not Found.');
        }
    }

    public function mailNotifications()
    {
        $date = Carbon::now();
        $records = Record::where('status', 'Submitted')->whereDate('updated_at', '<', $date)->get();
        foreach ($records as $record) {
            reminderMail('approver', $record);
        }

        $users = User::where('role', 'user')->get();
        foreach ($users as $user) {
            $now = Carbon::now()->subWeek();
            $weekOfYear = ($now->weekOfYear < 10) ? '0' . $now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year . '-W' . $weekOfYear;
            $date = Carbon::now();
            $date->setISODate($now->year, $weekOfYear);
            $startDate = $date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate = $date->endOfWeek()->format('Y-m-d H:i:s');
            $week=[];
            $now = Carbon::now();
            for($i=0;$i<5;$i++) {
                $now->subWeek();
                $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
                $currentWeek = $now->year.'-W'.$weekOfYear;
                $week[$i] = $currentWeek;
            }
            $times=Record::select('description as week')
                ->where('user_id',auth()->user()->clockify_id)
                ->whereIn('description', $week)
                ->where('record_type', 'timecard')->groupBy('description')->get();
            $time_weeks=[];
            foreach($times as $k=>$time){
                $time_weeks[$k] = $time->week;
            }
            $all_weeks=[];
            $allweeks=array_diff($week,$time_weeks);
            foreach ($allweeks as $k=>$w) {
                $all_weeks[]['week'] = $w;
            }
            $weekCount = count($all_weeks);
            if ($weekCount >= 1) {
                $user['weekCount'] = $weekCount;
                reminderMail('employee', $user);
            }
        }
        return response()->json(['message' => 'Reminder mail sent successfully.']);
    }
}
