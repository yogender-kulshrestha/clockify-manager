<?php

namespace App\Http\Controllers;

use App\Mail\CommonMail;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Project;
use App\Models\Record;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Clockify;
use Str;

class ClockifyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Clockify Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles clockify users and time entries for the application.
    | The controller uses a trait to conveniently provide user time entries to your applications.
    |
    */

    private $clockify;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->clockify = new Clockify(config('clockify.api_key'), config('clockify.workspace_name'));
    }

    /**
     * Get all workspaces from clockify
     */
    public function workspaces()
    {
        $workspaces = $this->clockify->apiRequest('workspaces');
        $rows = json_decode($workspaces);
        foreach($rows as $row) {
            $id=[
                'clockify_id' => $row->id,
            ];
            $input=[
                'name' => $row->name,
                'hourly_rate' => $row->hourlyRate,
                'memberships' => $row->memberships,
                'workspace_settings' => $row->workspaceSettings,
                'image_url' => $row->imageUrl,
                'feature_subscription_type' => $row->featureSubscriptionType,
            ];
            Workspace::updateOrCreate($id, $input);
        }
        return response()->json(['status' => true, 'message' => 'Workspace list updated successfully.']);
    }

    /**
     * Get all users from clockify
     */
    public function users()
    {
        $workspaces = Workspace::all();
        foreach ($workspaces as $workspace) {
            $users = $this->clockify->apiRequest('workspaces/'.$workspace->clockify_id.'/users');
            $users = json_decode($users);
            foreach ($users as $user) {
                $id = [
                    'clockify_id' => $user->id,
                ];
                $input = [
                    'clockify_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->profilePicture,
                    'memberships' => $user->memberships,
                    'settings' => $user->settings,
                    'status' => (Str::lower($user->status) == 'active') ? 'active' : 'inactive',
                ];
                $find = User::where('email', $user->email)->first();
                if (!$find) {
                    $random = Str::random(10);
                    $password = Hash::make($random);
                    $input['password'] = $password;
                }
                $insert = User::updateOrCreate($id, $input);
                $leave_types = LeaveType::all();
                foreach ($leave_types as  $lt) {
                    $lt_id = [
                        'user_id' => $user->id,
                        'leave_type_id' => $lt->id
                    ];
                    $lt_input = [
                        'created_at' => Carbon::now()
                    ];
                    LeaveBalance::updateOrCreate($lt_id, $lt_input);
                }
                if($insert->wasRecentlyCreated){
                    $data=$insert;
                    $email = $insert->email;
                    $name = $insert->name;
                    $data['to'] = $insert;
                    $data['owner'] = $insert;
                    $data['subject'] = 'Register Successfully.';
                    $data['title'] = 'Register Successfully.';
                    $data['body'] = 'You will successfully registered on Matthew Clockify Portal. <br/> <br/> Your login credentials here:-<br/>username: '.$insert->email.'<br/>password: '.$random;
                    \Mail::to($email, $name)->send(new CommonMail($data));
                }
            }
        }
        return response()->json(['status' => true, 'message' => 'User list updated successfully.']);
    }

    /**
     * Get all projects from clockify
     */
    public function projects()
    {
        $workspaces = Workspace::all();
        foreach ($workspaces as $workspace) {
            $rows = $this->clockify->apiRequest('workspaces/' . $workspace->clockify_id . '/projects');
            $rows = json_decode($rows);
            foreach ($rows as $row) {
                $input = [
                    'name' => $row->name,
                ];
                $id = [
                    'clockify_id' => $row->id,
                ];
                Project::updateOrCreate($id, $input);
            }
        }
        return response()->json(['status' => true, 'message' => 'Projects updated successfully.']);
    }

    /**
     * Get all time entries of current week from clockify
     */
    public function timeSheets()
    {
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $weekday = $dayOfTheWeek-1;
        if($dayOfTheWeek == 0) {
            $weekday = $dayOfTheWeek-1;
        }
        $start = Carbon::now()->startOfDay()->subDay($weekday)->format('Y-m-d\TH:i:s\Z');
        $workspaces = Workspace::get();
        foreach ($workspaces as $workspace) {
            $users = User::select('*')
                ->whereNotNull('clockify_id')->get();
            foreach ($users as $user) {
                $rows = $this->clockify->apiRequest('workspaces/'.$workspace->clockify_id.'/user/' . $user->clockify_id . '/time-entries?start='.$start);
                $rows = json_decode($rows);
                if(!empty($rows) && count($rows) > 0) {
                    foreach ($rows as $row) {
                        $startTime = Carbon::parse(date('Y-m-d H:i:s', strtotime($row->timeInterval->start)));
                        $endTime = Carbon::parse(date('Y-m-d H:i:s', strtotime($row->timeInterval->end ?? Carbon::now())));
                        $diff = $startTime->diffInSeconds($endTime);
                        $weekOfYear=($startTime->weekOfYear < 10) ? '0'.$startTime->weekOfYear : $startTime->weekOfYear;
                        $currentWeek = $startTime->year.'-W'.$weekOfYear;
                        if($weekOfYear == 52) {
                            if($startTime->format('d') < 7) {
                                $currentWeek = $startTime->subYear()->year.'-W'.$weekOfYear;
                            }
                        }
                        if($startTime > $endTime) {
                            $input = [
                                'description' => $row->description,
                                'tag_ids' => $row->tagIds,
                                'user_id' => $row->userId,
                                'billable' => $row->billable,
                                'task_id' => $row->taskId,
                                'project_id' => $row->projectId,
                                'week' => $currentWeek,
                                'start_time' => $endTime,
                                'end_time' => $startTime,
                                'duration_time' => $diff,
                                'duration' => $row->timeInterval->duration,
                                'workspace_id' => $row->workspaceId,
                                'is_locked' => $row->isLocked,
                                'custom_field_values' => $row->customFieldValues,
                            ];
                        } else {
                            $input = [
                                'description' => $row->description,
                                'tag_ids' => $row->tagIds,
                                'user_id' => $row->userId,
                                'billable' => $row->billable,
                                'task_id' => $row->taskId,
                                'project_id' => $row->projectId,
                                'week' => $currentWeek,
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                                'duration_time' => $diff,
                                'duration' => $row->timeInterval->duration,
                                'workspace_id' => $row->workspaceId,
                                'is_locked' => $row->isLocked,
                                'custom_field_values' => $row->customFieldValues,
                            ];
                        }
                        $id = [
                            'clockify_id' => $row->id,
                        ];
                        TimeSheet::updateOrCreate($id, $input);
                    }
                }
            }
        }
        return response()->json(['status' => true, 'message' => 'Time sheet updated successfully.']);
    }

    /**
     * Send reminder mail for submitting week report
     */
    public function mailNotifications()
    {
        $date = Carbon::now();
        $records = Record::where('status', 'Submitted')->whereDate('updated_at', '<', $date)->get();
        foreach ($records as $record) {
            reminderMail('approver', $record);
        }
        $users = User::where('role', 'user')->get();
        foreach ($users as $user) {
            $now = Carbon::now()->subWeek();
            $weekOfYear = ($now->weekOfYear < 10) ? '0' . $now->weekOfYear : $now->weekOfYear;
            $currentWeek = $now->year . '-W' . $weekOfYear;
            $date = Carbon::now();
            $date->setISODate($now->year, $weekOfYear);
            $startDate = $date->startOfWeek()->format('Y-m-d H:i:s');
            $endDate = $date->endOfWeek()->format('Y-m-d H:i:s');
            $week = [];
            $now = Carbon::now();
            for ($i = 0; $i < 5; $i++) {
                $now->subWeek();
                $weekOfYear = ($now->weekOfYear < 10) ? '0' . $now->weekOfYear : $now->weekOfYear;
                $currentWeek = $now->year . '-W' . $weekOfYear;
                $week[$i]['week'] = $currentWeek;
            }
            $time_weeks = Record::select('description as week')
                ->where('user_id', $user->clockify_id)
                ->whereIn('description', $week)
                ->where('record_type', 'timecard')->groupBy('description')->get()->toArray();
            $all_weeks = [];
            $allweeks = array_diff_key($week, $time_weeks);
            foreach ($allweeks as $w) {
                $all_weeks[] = $w;
            }
            $weekCount = count($all_weeks);
            if ($weekCount >= 1) {
                $user['weekCount'] = $weekCount;
                reminderMail('employee', $user);
            }
        }
        return response()->json(['message' => 'Reminder mail sent successfully.']);
    }
}
