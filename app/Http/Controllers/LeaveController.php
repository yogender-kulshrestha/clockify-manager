<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class LeaveController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Leave Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles employee leaves for the application.
    | The controller uses a trait to conveniently provide employee leaves records to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = Leave::where('user_id', auth()->user()->id)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-title="'.$query->title.'" data-leave_type_id="'.$query->leave_type_id.'" data-date_from="'.$query->date_from.'" data-date_to="'.$query->date_to.'" data-remarks="'.$query->remarks.'" data-status="'.$query->status.'" class="mx-1 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>';
                })->editColumn('status', function ($query) {
                    if($query->status == 'approved'){
                        $status = 'badge-success';
                    } elseif($query->status == 'in review'){
                        $status = 'badge-warning';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d M, Y');
                })->addColumn('leave_type', function ($query) {
                    return $query->leave_type->name ?? '';
                })
                ->rawColumns(['leave_type','status','action','created_at'])
                ->make(true);
        }
        $leave_categories = LeaveType::all();
        return view('leaves.index', compact('leave_categories'));
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
                'title' => 'required|min:3|max:255',
                'leave_type_id' => 'required|exists:leave_types,id',
                'date_from' => 'required',
                'date_to' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('title', 'leave_type_id', 'date_from', 'date_to', 'remarks');
            $input['user_id'] = auth()->user()->id;
            $id = [
                'id' => $request->id,
            ];
            $insert = Leave::updateOrCreate($id, $input);
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
    public function show($id)
    {
        //
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
        //
    }
}
