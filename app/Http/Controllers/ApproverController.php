<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Validator;

class ApproverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = User::withCount('employees')->where('role', 'user')->having('employees_count', '>', 0)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-name="'.$query->name.'" data-email="'.$query->email.'" data-employees="'.$query->employees.'" class="mx-1 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>
                    <a data-id="'.$query->id.'" class="mx-1 rowdelete" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                        <i class="fas fa-trash text-danger"></i>
                    </a>';
                })->editColumn('employees', function ($query) {
                    $employees='';
                    foreach ($query->employees as $key=>$employee) {
                        $employees.='<span class="badge badge-sm badge-primary mx-1">'.$employee->user->name.'</span>';
                    }
                    return $employees;
                })
                ->rawColumns(['action','employees'])
                ->make(true);
        }
        return view('approvers.index');
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
                'email' => 'required|email|max:255|unique:users,email,'.$request->id.',id',
                'status' => 'required|in:active,inactive',
                'password' => 'required_without:id|confirmed'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $input = $request->only('name', 'email', 'status');
            if($request->password) {
                $input['password']=Hash::make($request->password);
            }
            $id = [
                'id' => $request->id,
                'role' => 'hr',
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
        $data = User::find($id)->delete();
        if($data){
            return response()->json(['success' => true, 'message' => 'Deleted Successfully.'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion Failed.'], 200);
    }
}
