<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Site;
use App\Models\SiteManager;
use App\Models\Task;
use App\Models\VisaDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'    => 'required',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            'app_version' => 'required',
            'mobile_version' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $user = Employee::with(['visa_details'])->where('email', $request->username)->orwhere('number', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials');
        }

        $userCheckCode = VisaDetail::where('employee_id', $user->id)->where('country_code', $request->country_code)->first();

        if (!$userCheckCode) {
            return $this->error('Invalid credentials');
        }

        if ($user->status == 0) {
            return $this->error('This account is deleted. Please contact your administrator.');
        }

        $user->device_type = $request->device_type;
        $user->device_token = $request->device_token;
        $user->app_version = $request->app_version;
        $user->mobile_version = $request->mobile_version;
        $user->save();
        $token =  Auth::guard('employee')->login($user);
        $user['token'] = $token;

        $site = Site::where('id', $user->site_id)->first();

        $totalLeave = (int)LeaveType::where('employer_id', $site->employer_id)->sum('count');
        $totalTask = Task::where('employee_id', $user->id)->where('status', 'pending')->count();
        $approvedLeaves = Leave::where('employee_id', $user->id)
            ->where('status', 'approved')
            ->get();
        $closingLeave = null;
        foreach ($approvedLeaves as $leave) {
            $from = Carbon::parse($leave->from_date);
            $to = Carbon::parse($leave->to_date);
            if ($leave->time_range === 'half') {
                $closingLeave += 0.5;
            } else {
                $days = $from->diffInDaysFiltered(function (Carbon $date) {
                    return $date->isWeekday();
                }, $to) + 1;
                $closingLeave += $days;
            }
        }

        $availableLeave = $totalLeave - $closingLeave;
        if ($availableLeave < 0) {
            $availableLeave = 0;
        }
        $user['totalLeave'] = $totalLeave;
        $user['availableLeave'] = doubleval($availableLeave);
        $user['closingLeave'] = doubleval($closingLeave);
        $user['totalTask'] = $totalTask;

        return $this->success(true, 'Employee Login successfully.', $user);
    }

    public function changeEmployeePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
        ]);
        $user = Employee::find(auth()->user()->id);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        if (!Hash::check($request->current_password, $user->password)) {
            return  $this->error('Current password is incorrect.');
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->success(true, 'Password changed successfully.', []);
    }


    public function getEmployeeProfile(Request $request)
    {
        $user = auth()->user();
        $user = Employee::find(auth()->user()->id);
        return $this->success(true, 'Profile retrieved successfully.', $user);
    }


    public function editEmployeeProfile(Request $request)
    {
        $user = Employee::with('visa_details')->find(auth()->user()->id);

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable',
            'number'   => 'nullable',
            'visa_expiry_date' => 'nullable|date_format:d-m-Y',
            'visa_issue_date' => 'nullable|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }


        $email = Employee::where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($email) {
            return $this->error('Email is already taken by another employee.');
        }


        $number = Employee::where('number', $request->number)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($number) {
            return $this->error('Number is already taken by another employee.');
        }

        if ($request->hasFile('image')) {
            $oldImagePath = public_path('images/employee/' . $user->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/employee'), $imageName);
            File::copy(public_path('images/employee/' . $imageName), public_path('images/employee/selfie/' . $imageName));
            $user->image = $imageName;
            $user->selfie = $imageName;
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $visaDetails = VisaDetail::where('employee_id', $user->id)->first();
        if (!empty($request->visa_issue_date)) {
            $visaDetails->visa_issue_date = \Carbon\Carbon::createFromFormat('d-m-Y', $request->visa_issue_date)->format('Y-m-d');
        }

        if (!empty($request->visa_expiry_date)) {
            $visaDetails->visa_expiry_date = \Carbon\Carbon::createFromFormat('d-m-Y', $request->visa_expiry_date)->format('Y-m-d');
        }

        $visaDetails->visa_number = $request->visa_number;
        $visaDetails->share_code = $request->share_code;
        $visaDetails->country = $request->country;


        if ($request->hasFile('visa_document')) {
            $oldImagePath = public_path('images/employee/visa_document/' . $user->visa_document);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('visa_document')->extension();
            $request->file('visa_document')->move(public_path('images/employee/visa_document'), $imageName);
            $visaDetails->visa_document = $imageName;
        }

        $visaDetails->save();

        $user = Employee::with('visa_details')->find(auth()->user()->id);

        return $this->success(true, 'Employee profile updated successfully.', $user);
    }


    public function logout()
    {
        $user = Employee::find(auth()->user()->id);
        $user->device_type = null;
        $user->device_token = null;
        $user->save();
        $user = Auth::guard('employee')->logout(true);
        return $this->success(true, 'User Logout successfully.');
    }


    public function deleteAccount(Request $request)
    {
        $user = Employee::where('id', Auth::user()->id)->first();

        $user->status = 0;
        $user->save();
        return $this->success(true, 'Account deleted successfully.');
    }


    public function forgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = Employee::where('number', $request->phone)->orWhere('email', $request->phone)->first();

        if (!$data) {
            return $this->error('No user found with this phone number.');
        }


        $userCheckCode = VisaDetail::where('employee_id', $data->id)->where('country_code', $request->country_code)->first();

        if (!$userCheckCode) {
            return $this->error('Country code is not matched.');
        }


        $data->password = Hash::make($request->password);

        $data->save();

        return $this->success(true, 'Password updated successfully.');
    }


    public function phoneVerification(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $data = Employee::where('number', $request->phone)->orWhere('email', $request->phone)->first();

        if (!$data) {
            return $this->error('No user found with this phone number.');
        }

        return $this->success(true, 'Verification successfully.', $data);
    }


    public function  manageAppConstants()
    {
        $data = [
            'appVersion' => '1.0.0',   // static value
            'iOSAppVersion' => '1.0.0', // static value
            'eventStatusCode' => 1001,  // static value (default)
        ];

        return $this->success(true, 'manage app constants.',$data);
    }
}
