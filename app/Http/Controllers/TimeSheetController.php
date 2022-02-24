<?php

namespace App\Http\Controllers;

use App\Models\TimeSheet;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Str;

class TimeSheetController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TimeSheet Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles time entries for the application.
    | The controller uses a trait to conveniently provide time entries records to your applications.
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
            $user_id=auth()->user()->clockify_id;
            $seletedWeek = explode('-',$request->seletedWeek);
            $date = Carbon::now();
            $date->setISODate($seletedWeek[0],Str::replace('W', '', $seletedWeek[1]));
            $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');
            $newDate=Carbon::parse($date->startOfWeek()->format('Y-m-d H:i:s'));
            $one=Carbon::parse($date->startOfWeek());
            $two=Carbon::parse($newDate->addDays());
            $three=Carbon::parse($newDate->addDays());
            $four=Carbon::parse($newDate->addDays());
            $five=Carbon::parse($newDate->addDays());
            $six=Carbon::parse($newDate->addDays());
            $seven=Carbon::parse($date->endOfWeek());
            $data=TimeSheet::select('project_id')->groupBy('project_id')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('project', function($query){
                    return $query->project->name ?? '';
                })
                ->addColumn('action', function($query){
                    return '<a data-id="'.$query->id.'" data-name="'.$query->name.'" class="mx-3 rowedit" data-bs-toggle="modal" data-bs-target="#modal-create" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                        <i class="fas fa-edit text-secondary"></i>
                    </a>';
                })->editColumn('status', function ($query) {
                    if($query->status == 'active'){
                        $status = 'badge-success';
                    } else {
                        $status = 'badge-danger';
                    }
                    return '<span class="badge badge-sm '.$status.'">'.$query->status.'</span>';
                })->addColumn('one', function ($query) use($user_id,$one){
                    $start = $one->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $one->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('two', function ($query) use($user_id,$two){
                    $start = $two->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $two->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('three', function ($query) use($user_id,$three){
                    $start = $three->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $three->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('four', function ($query) use($user_id,$four){
                    $start = $four->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $four->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('five', function ($query) use($user_id,$five){
                    $start = $five->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $five->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('six', function ($query) use($user_id,$six){
                    $start = $six->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $six->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('seven', function ($query) use($user_id,$seven){
                    $start = $seven->copy()->startOfDay()->format('Y-m-d H:i:s');
                    $end = $seven->copy()->endOfDay()->format('Y-m-d H:i:s');
                    return total_hours($user_id, $query->project_id, $start, $end);
                })->addColumn('total', function ($query) use($user_id, $startDate, $endDate){
                    return total_hours($user_id, $query->project_id, $startDate, $endDate);
                })
                ->rawColumns(['status','action','project','one','two','three','four','six','seven','total','created_at'])
                ->make(true);
        }
        $now = Carbon::now();
        $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
        $currentWeek = $now->year.'-W'.$weekOfYear;
        return view('time-sheets.index', compact('currentWeek'));
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
