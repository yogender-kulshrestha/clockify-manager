<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationMail;
use App\Models\User;
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
    public function index()
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

    public function report()
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

    public function timeSheets(Request $request)
    {
        $userId='61c1c9ad7072ea24657c1d0b';
        return $this->clockify->apiRequest('workspaces/'.$this->clockify->workspaceId.'/user/'.$userId.'/time-entries');
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
