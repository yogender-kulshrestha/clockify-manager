<?php

namespace App\Http\Controllers;

use App\Models\EmailAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class EmailAlertController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Alert Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles leave type for the application.
    | The controller uses a trait to conveniently provide email
    | alerts status to your applications.
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
            $data = EmailAlert::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($query) {
                    return $query->status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        return view('email-alerts.index');
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
                'id' => 'required',
                'status' => 'required|in:1,0',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 200);
            }
            $input = $request->only('status');
            $id = $request->only('id');
            $insert = EmailAlert::where($id)->update($input);
            if ($insert) {
                return response()->json(['success' => true, 'message' => 'Updated Successfully.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Updating Failed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 200);
        }
    }
}
