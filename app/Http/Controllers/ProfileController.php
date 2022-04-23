<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Profile Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling update password.
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
     * Display a listing of the profile resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employee.profile');
    }

    /**
     * Store updated password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'old_password' => 'required',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }

            $user=User::find(auth()->user()->id);
            if (!Hash::check($request->old_password, $user->password)) {
                $error = [
                    "old_password" => ["The old password is not matched with our record."]
                ];
                return response()->json(['success'=>false, 'errors' => $error, 'message' => 'Something went wrong.'], 422);
            }

            $password=Hash::make($request->password);
            $insert = User::where('id',auth()->user()->id)->update(['password' => $password]);
            if ($insert) {
                return response()->json(['success' => true, 'message' => 'Password Updated Successfully.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Password Updating Failed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 200);
        }
    }

    /**
     * Store updated profile image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function profile_image(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|min:3',
                'profile_image' => 'nullable|mimes:jpg,png,jpeg,gif',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag(), 'message' => 'Something went wrong.'], 422);
            }

            $input = $request->only('name');
            if($request->hasFile('profile_image')) {
                $imageName = $request->profile_image->store('images/profile');
                $input['image'] = asset('storage/' . $imageName);
            }
            $insert = User::where('id',auth()->user()->id)->update($input);
            if ($insert) {
                return response()->json(['success' => true, 'message' => 'Profile updated successfully.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Profile updating failed.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 200);
        }
    }
}
