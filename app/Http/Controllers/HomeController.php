<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Record;
use App\Models\Setting;
use App\Models\TimeCard;
use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Time;
use Validator;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles admin home and setting for the application.
    | The controller uses a trait to conveniently provide admin interface to your applications.
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
     * Admin dashboard.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * View settings
     */
    public function settings()
    {
        return view('settings');
    }

    /**
     * Update settings
     */
    public function settingsPost(Request $request)
    {
        $request->validate([
            'working_time_from' => 'required',
            'working_time_to' => 'required|after:working_time_from',
            'overclocking_hours' => 'required|numeric|min:1|max:24',
            'weekly_hours' => 'required|numeric|min:1|max:84'
        ]);

        $input = $request->only('working_time_from', 'working_time_to', 'overclocking_hours', 'weekly_hours');
        Setting::query()->update($input);
        return redirect()->back()->withSuccess('Setting updated successfully.');
    }

    /**
     * Delete all records
     */
    public function deleteAllRecords(){
        try {
            TimeCard::query()->delete();
            TimeSheet::query()->delete();
            Leave::query()->delete();
            Record::query()->delete();
            return response()->json(['success' => true, 'message' => 'All records deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Delete all records of a specific user
     */
    public function deleteAllRecordsByUser(Request $request){
        try {
            $rules = [
                'user_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $error = '';
                if (!empty($validator->errors())) {
                    $error = $validator->errors()->first();
                }
                return response()->json(['success' => false, 'message' => $error]);
            }
            $user_id = $request->user_id;
            TimeCard::where('user_id', $user_id)->delete();
            TimeSheet::where('user_id', $user_id)->delete();
            Leave::where('user_id', $user_id)->delete();
            Record::where('user_id', $user_id)->delete();
            return response()->json(['success' => true, 'message' => 'All records deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Delete all users
     */
    public function deleteAllUsers(){
        try {
            User::where('role', 'user')->delete();
            TimeCard::query()->delete();
            TimeSheet::query()->delete();
            Leave::query()->delete();
            Record::query()->delete();
            return response()->json(['success' => true, 'message' => 'All users deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Delete a specific user
     */
    public function deleteByUserId(Request $request){
        try {
            $rules = [
                'user_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $error = '';
                if (!empty($validator->errors())) {
                    $error = $validator->errors()->first();
                }
                return response()->json(['success' => false, 'message' => $error]);
            }
            $user_id = $request->user_id;
            User::where('clockify_id', $user_id)->delete();
            TimeCard::where('user_id', $user_id)->delete();
            TimeSheet::where('user_id', $user_id)->delete();
            Leave::where('user_id', $user_id)->delete();
            Record::where('user_id', $user_id)->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
