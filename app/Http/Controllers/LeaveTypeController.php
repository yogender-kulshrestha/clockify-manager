<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class LeaveTypeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LeaveType Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles leave type for the application.
    | The controller uses a trait to conveniently provide leave type records to your applications.
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
            $data = LeaveType::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-name="'.$query->name.'" data-balance="'.$query->balance.'" class="mx-1 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>';
                })->editColumn('created_at', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->created_at)->format('d M, Y');
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('leave-types.index');
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
                //'type' => 'required|numeric|min:0'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('name', 'balance');
            $id = [
                'id' => $request->id,
            ];
            $insert = LeaveType::updateOrCreate($id, $input);
            if ($insert) {
                $message = $request->id ? 'Updated Successfully.' : 'Added Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);
            }
            $message = $request->id ? 'Updating Failed.' : 'Adding Failed.';
            return response()->json(['success' => false, 'message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 200);
        }
    }

    public function destroy($id)
    {
        $data = LeaveType::find($id)->delete();
        if($data){
            return response()->json(['success' => true, 'message' => 'Deleted Successfully.'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion Failed.'], 200);
    }
}
