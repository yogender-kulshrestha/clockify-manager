<?php

namespace App\Http\Controllers;

use App\Models\Approver;
use App\Models\Leave;
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
            $week[$i]['week'] = $currentWeek;
        }
        $time_weeks=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)
            ->whereIn('description', $week)
            ->where('record_type', 'timecard')->groupBy('description')->get()->toArray();
        $all_weeks=[];
        $allweeks=array_diff_key($week,$time_weeks);
        foreach ($allweeks as $w){
            $all_weeks[] = $w;
        }
        $weekCount=count($all_weeks);
        if($weekCount == 1) {
            $currentWeek = $all_weeks[0]->week;
        }
        return view('employee.home', compact('weekCount','currentWeek', 'startDate', 'endDate'));
    }

    public function records(Request $request)
    {
        if($request->ajax()) {
            $approving = Approver::select('user_id')->where('approver_id', auth()->user()->clockify_id)->get();
            $data = Record::where('user_id', auth()->user()->clockify_id)->orWhereIn('user_id', $approving)->latest()->get();
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
                            if($query->status == 'Revise and Resubmit'){
                                $action='<a href="'.route('employee.timecard.edit',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    } else {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Submitted'){
                                $action='<a href="'.route('employee.leave.review',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Submitted'){
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
        return view('employee.records');
    }

    public function requestLeave(Request $request)
    {
        $leave_categories = LeaveType::all();
        return view('employee.leave', compact('leave_categories'));
    }

    public function storeRequestLeave(Request $request)
    {
        try {
            $rules = [
                //'leave_type_id' => 'required_if_null:id|required|exists:leave_types,id',
                //'date_from' => 'required_if_null:id|required',
                //'date_to' => 'required_if_null:id|required',
                'status' => 'required|in:Submitted,Revise and Resubmit,Approved',
                'user_id' => 'required'
            ];
            $messages = [
                'leave_type_id.required' => 'The leave type field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('title', 'user_id', 'leave_type_id', 'date_from', 'date_to', 'remarks', 'status');
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
                Record::create($record);
                return response()->json(['success' => true, 'message' => 'Request leave create successfully.'], 200);
            } else {
                $record = [
                    'record_type' => 'leave',
                    'description' => $insert->id,
                ];
                Record::where($record)->update(['status' => $request->status]);
                return response()->json(['success' => true, 'message' => 'Request leave update successfully.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Request leave failed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
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
            return view('employee.leave-edit', compact('data', 'leave_categories'));
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
                    return '<a data-id="'.$query->id.'" data-remarks="'.$query->employee_remarks.'" data-description="'.$query->description.'" data-start_time="'.Carbon::parse($query->start_time)->format('Y-m-d\TH:i:s').'" data-end_time="'.Carbon::parse($query->end_time)->format('Y-m-d\TH:i:s').'" class="rowedit btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        Edit
                    </a>
                    <a data-id="'.$query->id.'" data-exception="'.$query->exception.'" data-description="'.$query->description.'" data-start_time="'.$query->start_time.'" data-end_time="'.$query->end_time.'" class="exception btn btn-dark btn-sm">
                        '.$exception.' Exception
                    </a>';
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
                })
                ->rawColumns(['status','action','start_date','start_time','end_date','end_time','time_duration','created_at'])
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
                'description' => 'required|max:255',
                'start_time' => 'required',
                'end_time' => 'required',
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
        $count = TimeSheet::where('start_time', '>=', $request->start_time)->where('start_time', '<=', $request->end_time)
            ->whereIn('time_error', ['1','2','3','4'])->where('exception', '!=', '1')->count();
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
            $net_hours = 0;
            $employee_remarks = '';
            $exception = '0';
            foreach ($sheets as $sheet) {
                $flags .= $sheet->description ? $sheet->description.', ' : '';
                $net_hours += $sheet->duration_time ?? 0;
                $employee_remarks .= $sheet->employee_remarks ? $sheet->employee_remarks.', ' : '';
                if($sheet->exception == '1') {
                    $exception = '1';
                }
            }
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
            return view('employee.timecard-submit', compact('week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours','leave_hours','nleave_hours'));
        }
        return redirect()->back()->withError('Please create timecard first.');
    }

    public function submitTimeCard(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Submitted,Revise and Resubmit,Approved',
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
            return redirect()->to(route('employee.records'))->withSuccess('Timecard submit successfully.');
        } else {
            return redirect()->to(route('employee.records'))->withSuccess('Timecard resubmit successfully.');
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
            $net_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hours = $dt->diffInHours($dt->copy()->addSeconds($net_hours));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            return view('employee.timecard-view', compact('data','week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours'));
        }
        return redirect()->to(route('employee.records'))->withError('Please create timecard first.');
    }

    public function editTimecard($id)
    {
        $data = Record::where('record_type', 'timecard')->where('id', $id)->first();
        if($data !== null) {
            $week=$data->description;
            $currentWeek=$week;
            $rows=TimeCard::where('user_id', $data->user_id)->where('week', $week)->get();
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->where('user_id', $user_id)->sum('unpaid_hours');
            $net_hours = $dt->diffInHours($dt->copy()->addSeconds($net_hours));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            return view('employee.timecard-edit', compact('data','week','currentWeek','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours'));
        }
        return redirect()->to(route('employee.records'))->withError('Please create timecard first.');
    }

    public function reviewTimecard($id)
    {
        $data = Record::where('record_type', 'timecard')->where('id', $id)->first();
        if($data !== null) {
            $week=$data->description;
            $rows=TimeCard::where('user_id', $data->user_id)->where('week', $week)->get();
            $seletedWeek = explode('-',Str::replace('W','',$week));
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],$seletedWeek[1]);
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');

            $dt = Carbon::now();
            $net_hours = TimeCard::where('week', $week)->groupBy('week')->sum('net_hours');
            $ot_hours = TimeCard::where('week', $week)->groupBy('week')->sum('ot_hours');
            $short_hours = TimeCard::where('week', $week)->groupBy('week')->sum('short_hours');
            $unpaid_hours = TimeCard::where('week', $week)->groupBy('week')->sum('unpaid_hours');
            $net_hours = $dt->diffInHours($dt->copy()->addSeconds($net_hours));
            $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($ot_hours));
            $short_hours = $dt->diffInHours($dt->copy()->addSeconds($short_hours));
            $unpaid_hours = $dt->diffInHours($dt->copy()->addSeconds($unpaid_hours));
            return view('employee.timecard-review', compact('data','week','startDate','endDate','rows', 'net_hours', 'ot_hours', 'short_hours', 'unpaid_hours'));
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
            $week[$i]['week'] = $currentWeek;
        }
        $time_weeks=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)
            ->whereIn('description', $week)
            ->where('record_type', 'timecard')->groupBy('description')->get()->toArray();
        $all_weeks=[];
        $allweeks=array_diff_key($week,$time_weeks);
        foreach ($allweeks as $w){
            $all_weeks[] = $w;
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
                    return '<a data-id="'.$query->id.'" data-name="'.$query->name.'" data-email="'.$query->email.'" data-status="'.$query->status.'" class="mx-1 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>
                    <a href="'.route('employees.show',['employee'=>$query->clockify_id]).'" data-bs-toggle="tooltip" data-bs-original-title="View">
                        <i class="fas fa-eye text-success"></i>
                    </a>
                    <!--<a data-id="'.$query->id.'" class="mx-1 rowdelete" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                        <i class="fas fa-trash text-danger"></i>
                    </a>-->';
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
                'status' => 'required|in:active,inactive',
                'password' => 'required_without:id|confirmed'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('name', 'status');
            if($request->password) {
                $input['password']=Hash::make($request->password);
            }
            $id = [
                'id' => $request->id,
                'role' => 'user',
            ];
            $insert = User::updateOrCreate($id, $input);
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
                            if($query->status == 'Revise and Resubmit'){
                                $action='<a href="'.route('employee.timecard.edit',["week"=>$query->id]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["week"=>$query->id]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        }
                    } else {
                        if($query->record_type == 'leave'){
                            if($query->status == 'Submitted'){
                                $action='<a href="'.route('employee.leave.review',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.leave.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
                            }
                        } else {
                            if($query->status == 'Submitted'){
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
            return view('employees.show', compact('data'));
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
}
