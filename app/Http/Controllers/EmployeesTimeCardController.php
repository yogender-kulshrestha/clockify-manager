<?php

namespace App\Http\Controllers;

use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;

class EmployeesTimeCardController extends Controller
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
            if($request->seletedWeek) {
                $seletedWeek = explode('-', Str::replace('W', '', $request->seletedWeek));
                $date = Carbon::now();
                $date->setISODate($seletedWeek[0], $seletedWeek[1]);
                $startDate = $date->startOfWeek()->format('Y-m-d H:i:s');
                $endDate = $date->endOfWeek()->format('Y-m-d H:i:s');
            }
            $data = TimeSheet::where('user_id', $request->user_id)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-name="'.$query->name.'" class="mx-3 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
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
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('d M, Y');
                })->editColumn('start_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->start_time)->format('H:i:s A');
                })->addColumn('end_date', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('d M, Y');
                })->editColumn('end_time', function ($query) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $query->end_time)->format('H:i:s A');
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
        $users = my_employees();//User::where('role', 'user')->get();
        return view('employees-time-cards.index', compact('users'));
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
        //
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
