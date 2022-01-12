<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /*public function index()
    {
        return view('home');
    }*/

    public function index()
    {
        $now = Carbon::now()->subWeek();
        $weekOfYear=($now->weekOfYear < 10) ? '0'.$now->weekOfYear : $now->weekOfYear;
        $currentWeek = $now->year.'-W'.$weekOfYear;
        $date = Carbon::now();
        $date->setISODate($now->year,$weekOfYear);
        $startDate=$date->startOfWeek()->format('Y-m-d H:i:s');
        $endDate=$date->endOfWeek()->format('Y-m-d H:i:s');
        $time_weeks=Record::select('description as week')
            ->where('user_id',auth()->user()->clockify_id)->where('record_type', 'timecard')->groupBy('description')->get();
        $date2 = Carbon::now();
        $weekOfYear2=($date2->weekOfYear < 10) ? '0'.$date2->weekOfYear : $date2->weekOfYear;
        $currentWeek2 = $date2->year.'-W'.$weekOfYear2;
        $all_weeks=TimeSheet::select('week')
            ->where('user_id',auth()->user()->clockify_id)
            ->where('week', '!=', $currentWeek2)
            ->whereNotIn('week', $time_weeks)
            ->whereNotNull('week')
            ->where('week', '!=', '')
            ->where('week', '!=', ' ')
            ->groupBy('week')->get();
        $weekCount=$all_weeks->count();
        if($weekCount == 1) {
            $currentWeek = $all_weeks[0]->week;
        }
        return view('employee.home', compact('weekCount','currentWeek', 'startDate', 'endDate'));
    }
}
