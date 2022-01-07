<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationMail;
use App\Models\Project;
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
    private $clockify;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $apiEndpoint='https://api.clockify.me/api/v1';
//        $reportsApiEndpoint='https://reports.api.clockify.me/v1';
        $this->clockify = new Clockify(config('clockify.api_key'), config('clockify.workspace_name'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = $this->clockify->apiRequest('workspaces/'.$this->clockify->workspaceId.'/users');
        $users = json_decode($users);
        foreach($users as $user) {
            $id=[
                'email' => $user->email,
            ];
            $input=[
                'clockify_id' => $user->id,
                'name' => $user->name,
                'image' => $user->profilePicture,
                'memberships' => $user->memberships,
                'settings' => $user->settings,
                'status' => Str::lower($user->status),
            ];
            $find = User::where('email', $user->email)->first();
            if(!$find) {
                $random='12345678';//Str::random(10);
                $password=Hash::make($random);
                $input['password'] = $password;
            }
            $insert=User::updateOrCreate($id, $input);
            dispatch(new SendRegistrationMail($insert))->onQueue('mail');
        }
        return response()->json(['status' => true, 'message' => 'User list updated successfully.']);
    }

    public function report(Request $request)
    {
        return $this->clockify->getReportByDay(Carbon::now()->format('Y-m-d'));
        $data = [
            "archived" => "Active",
            "billable" => "BOTH",
            "clientIds" => [],
            "description" => "",
            "endDate" => "2018-10-01T23:59:59.999Z",
            "firstTime" => true,
            "includeTimeEntries" => true,
            "me" => false,
            "name" => "",
            "projectIds" => [],
            "startDate" => "2018-10-01T00:00:00.000Z",
            "tagIds" => [],
            "taskIds" => [],
            "userGroupIds" => [],
            "userIds" => [],
            "zoomLevel" => "week"
        ];
        return $this->clockify->apiRequest('workspaces/'.$this->clockify->workspaceId.'/reports/summary/', json_encode($data));
    }

    public function project(Request $request)
    {
        $rows = $this->clockify->apiRequest('workspaces/' . $this->clockify->workspaceId . '/projects');
        $rows=json_decode($rows);
        foreach ($rows as $row){
            $input = [
                'name' => $row->name,
            ];
            $id = [
                'clockify_id' => $row->id,
            ];
            Project::updateOrCreate($id,$input);
        }
        return response()->json(['status' => true, 'message' => 'Projects updated successfully.']);
    }

    public function timeSheet(Request $request)
    {
        $users = User::whereNotNull('clockify_id')->get();
        foreach ($users as $user) {
            $rows = $this->clockify->apiRequest('workspaces/' . $this->clockify->workspaceId . '/user/' . $user->clockify_id . '/time-entries');
            $rows=json_decode($rows);
            foreach ($rows as $row){
                $startTime=Carbon::parse(date('Y-m-d h:i:s', strtotime($row->timeInterval->start)));
                $endTime=Carbon::parse(date('Y-m-d h:i:s', strtotime($row->timeInterval->end)));
                $diff = $startTime->diff($endTime)->format('%H:%I:%S');
                $input = [
                    'description' => $row->description,
                    'tag_ids' => $row->tagIds,
                    'user_id' => $row->userId,
                    'billable' => $row->billable,
                    'task_id' => $row->taskId,
                    'project_id' => $row->projectId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_time' => $diff,
                    'duration' => $row->timeInterval->duration,
                    'workspace_id' => $row->workspaceId,
                    'is_locked' => $row->isLocked,
                    'custom_field_values' => $row->customFieldValues,
                ];
                $id = [
                    'clockify_id' => $row->id,
                ];
                TimeSheet::updateOrCreate($id,$input);
            }
        }
        return response()->json(['status' => true, 'message' => 'Time sheet updated successfully.']);
    }

    public function workspaces(Request $request)
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
            $insert=Workspace::updateOrCreate($id, $input);
        }
        return response()->json(['status' => true, 'message' => 'Workspace list updated successfully.']);
    }

    public function users(Request $request)
    {
        $workspaces = Workspace::all();
        foreach ($workspaces as $workspace) {
            $users = $this->clockify->apiRequest('workspaces/'.$workspace->clockify_id.'/users');
            $users = json_decode($users);
            foreach ($users as $user) {
                $id = [
                    'email' => $user->email,
                ];
                $input = [
                    'clockify_id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->profilePicture,
                    'memberships' => $user->memberships,
                    'settings' => $user->settings,
                    'status' => Str::lower($user->status),
                ];
                $find = User::where('email', $user->email)->first();
                if (!$find) {
                    $random = '12345678';//Str::random(10);
                    $password = Hash::make($random);
                    $input['password'] = $password;
                }
                $insert = User::updateOrCreate($id, $input);
                dispatch(new SendRegistrationMail($insert))->onQueue('mail');
            }
        }
        return response()->json(['status' => true, 'message' => 'User list updated successfully.']);
    }

    public function projects(Request $request)
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

    public function timeSheets(Request $request)
    {
        $workspaces = Workspace::get();
        foreach ($workspaces as $workspace) {
            $users = User::whereNotNull('clockify_id')//->get();
                ->whereIn('clockify_id', ['609935adba9fdd7cafab3447','60aaf97e79793e3042ff8975'])->get();
            foreach ($users as $user) {
                $rows = $this->clockify->apiRequest('workspaces/'.$workspace->clockify_id.'/user/' . $user->clockify_id . '/time-entries');
                $rows = json_decode($rows);
                if(!empty($rows) && count($rows) > 0) {
                    foreach ($rows as $row) {
                        $startTime = Carbon::parse(date('Y-m-d h:i:s', strtotime($row->timeInterval->start)));
                        $endTime = Carbon::parse(date('Y-m-d h:i:s', strtotime($row->timeInterval->end)));
                        $diff = $startTime->diffInSeconds($endTime);
                        $input = [
                            'description' => $row->description,
                            'tag_ids' => $row->tagIds,
                            'user_id' => $row->userId,
                            'billable' => $row->billable,
                            'task_id' => $row->taskId,
                            'project_id' => $row->projectId,
                            'start_time' => $endTime,
                            'end_time' => $startTime,
                            'duration_time' => $diff,
                            'duration' => $row->timeInterval->duration,
                            'workspace_id' => $row->workspaceId,
                            'is_locked' => $row->isLocked,
                            'custom_field_values' => $row->customFieldValues,
                        ];
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
}
