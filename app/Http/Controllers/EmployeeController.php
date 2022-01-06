<?php

namespace App\Http\Controllers;

use App\Models\Approver;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Record;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Validator;
use DB;

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
        return view('employee.home');
    }

    public function records(Request $request)
    {
        if($request->ajax()) {
            $data = Record::where('user_id', auth()->user()->clockify_id)->orWhereIn('user_id', Approver::select('user_id')->where('approver_id', auth()->user()->clockify_id)->get())->latest()->get();
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
                                $action='<a href="'.route('employee.timecard.edit',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Edit</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
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
                                $action='<a href="'.route('employee.timecard.review',["id"=>$query->description]).'" class="btn btn-dark btn-sm">Review</a>';
                            } else {
                                $action='<a href="'.route('employee.timecard.view',["id"=>$query->description]).'" class="btn btn-dark btn-sm">View</a>';
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
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d M, Y');
                })->editColumn('updated_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->updated_at)->format('d M, Y');
                })->editColumn('description', function ($query) {
                    if($query->type == 'leave'){
                        return leave_description($query->description);
                    } else {
                        return timecard_description($query->description);
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
            ];
            $messages = [
                'leave_type_id.required' => 'The leave type field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('title', 'leave_type_id', 'date_from', 'date_to', 'remarks', 'status');
            $input['user_id'] = auth()->user()->clockify_id;
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
                    'user_id' => auth()->user()->clockify_id,
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

    public function timecard(Request $request)
    {
        return view('employee.timecard');
    }

    public function timesheet(Request $request)
    {
        return view('employee.timesheet');
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
                    <a href="'.route('employees.show',['employee'=>$query->id]).'" data-bs-toggle="tooltip" data-bs-original-title="View">
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                ->addIndexColumn()
                ->addColumn('action', function ($query) {
                    return '<a data-id="' . $query->id . '" data-record_type="' . $query->record_type . '" data-description="' . $query->description . '" data-remarks="' . $query->remarks . '" data-status="' . $query->status . '" class="mx-1 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>
                    <!--<a data-id="' . $query->id . '" class="mx-1 rowdelete" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                        <i class="fas fa-trash text-danger"></i>
                    </a>-->';
                })->editColumn('status', function ($query) {
                    if ($query->status == 'active') {
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm ' . $status . '">' . $query->status . '</span>';
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d M, Y');
                })->editColumn('updated_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->updated_at)->format('d M, Y');
                })
                ->rawColumns(['status', 'action', 'created_at', 'updated_at'])
                ->make(true);
        }
        $data = User::find($id);
        if ($data) {
            return view('employees.show', compact('data'));
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = User::find($id)->delete();
        if($data){
            return response()->json(['success' => true, 'message' => 'Deleted Successfully.'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion Failed.'], 200);
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
}
