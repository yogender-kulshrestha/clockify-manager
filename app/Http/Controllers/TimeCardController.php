<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Validator;

class TimeCardController extends Controller
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
            $data = TimeSheet::query();
            if($request->date_from && $request->date_to) {
                $data->whereDate('start_time', '>=', $request->date_from)->whereDate('start_time', '<=', $request->date_to);
            }
            $data = $data->where('user_id', auth()->user()->clockify_id)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-project_id="'.$query->project_id.'" data-description="'.$query->description.'" data-start_time="'.$query->start_time.'" data-end_time="'.$query->end_time.'" class="mx-3 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-secondary"></i>
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
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('Y-m-d');
                })->editColumn('start_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('H:i:s');
                })->addColumn('end_date', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('Y-m-d');
                })->editColumn('end_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('H:i:s');
                })->addColumn('time_duration', function ($query) {
                    /*$date_from = Carbon::parse($query->start_time);
                    $date_to = Carbon::parse($query->end_time);
                    $diff = $date_from->diff($date_to)->format('%H:%I:%S');
                    return $diff;*/
                    return $query->duration_time;
                })
                ->rawColumns(['status','action','start_date','start_time','end_date','end_time','time_duration','created_at'])
                ->make(true);
        }
        $projects = Project::all();
        return view('time-cards.index', compact('projects'));
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
                'project_id' => 'required',
                'description' => 'required|max:255',
                'duration' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }
            $date_from = Carbon::parse($request->start_time);
            $date_to = Carbon::parse($request->end_time);
            $diff = $date_from->diff($date_to)->format('%H:%I:%S');
            $input = $request->only('project_id', 'description');
            $duration = explode(' - ',  $request->duration);
            $input['start_time'] = Carbon::parse($duration[0])->format('Y-m-d H:i:s');
            $input['end_time'] = Carbon::parse($duration[1])->format('Y-m-d H:i:s');
            $input['duration']=$diff;
            $input['duration_time']=$diff;
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
