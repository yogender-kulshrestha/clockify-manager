<?php

namespace App\Http\Controllers;

use App\Models\Approver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Validator;

class ApproverController extends Controller
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
                    return '<a href="'.route('approvers.edit',['approver'=>$query->id]).'" data-id="'.$query->id.'" data-name="'.$query->name.'" data-email="'.$query->email.'" data-employees="'.$query->employees.'" class="mx-1 rowedit1" data-bs-toggle1="modal" data-bs-target1="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-primary"></i>
                    </a>
                    <a data-id="'.$query->clockify_id.'" class="mx-1 rowdelete" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                        <i class="fas fa-trash text-danger"></i>
                    </a>';
                })->editColumn('employees', function ($query) {
                    /*$employees='';
                    foreach ($query->employees as $key=>$employee) {
                        $employees.='<span class="badge badge-sm badge-primary mx-1">'.$employee->user->name.'</span>';
                    }
                    return $employees;*/
                    return $query->employees->count() ?? 0;
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
        $notIn = Approver::select('approver_id')->groupBy('approver_id')->get();
        $approvers = User::where('role', 'user')->whereNotIn('clockify_id', $notIn)->get();
        return view('approvers.create', compact('approvers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'approver_id' => 'required',
            'employees' => 'required|array',
        ];
        $messages=[
            'approver_id.required' => 'The approver field is required.'
        ];
        $request->validate($rules, $messages);

        $input = $request->only('approver_id');
        Approver::where($input)->delete();
        foreach ($request->employees as $employee) {
            $input['user_id'] = $employee;
            $insert = Approver::create($input);
        }
        if ($insert) {
            return redirect()->to(route('approvers.index'))->withSuccess('Approver assign successfully.');
        }
        return redirect()->back()->withError('Approver assigning Failed.')->withInput();
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
        $data = User::where('role', 'user')->where('id', $id)->first();
        if($data !== null){
            $approvers = Approver::select('user_id')->where('approver_id', '!=', $data->clockify_id)->get();
            $users= User::where('role', 'user')->whereNotIn('clockify_id', $approvers)->where('id', '!=', $id)->get();
            $semployees=Approver::select('user_id')->where('approver_id', $data->clockify_id)->get();
            $employees=[];
            foreach ($semployees as $e){
                $employees[] = $e->user_id;
            }
            return view('approvers.edit', compact('data', 'users', 'employees'));
        }
        abort(404);
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
        $rules = [
            'employees' => 'required|array',
        ];
        $request->validate($rules);
        Approver::where('approver_id', $id)->delete();
        $input=['approver_id'=>$id];
        foreach ($request->employees as $employee) {
            $input['user_id'] = $employee;
            $insert = Approver::create($input);
        }
        if ($insert) {
            return redirect()->to(route('approvers.index'))->withSuccess('Updated Successfully.');
        }
        return redirect()->back()->withError('Updating Failed.')->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Approver::where('approver_id', $id)->delete();
        if($data){
            return response()->json(['success' => true, 'message' => 'Deleted Successfully.'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Deletion Failed.'], 200);
    }
}
