<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\SiteManager;
use App\Models\VisaDetail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SiteController extends Controller
{
    // List all sites
    public function index(Request $request)
    {

        $limit = $request->limit;
        $offset = $request->offset;

        $query = Site::with(['siteManager'])
            ->where('employer_id', auth()->user()->id)
            ->orderBy('created_at', 'desc');

        if (!empty($limit)) {
            $query->skip($offset * $limit)->take($limit);
        }
        $sites = $query->get()->map(function ($site) {
            $site['total_employee'] = Employee::where('site_id', $site->id)->count();
            return $site;
        });

        return $this->success(true, 'Sites retrieved successfully.', $sites);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required',
            'address'            => 'nullable',
            'email'         => 'required|email|unique:sites,email',
            'phone'         => 'required|unique:sites,phone',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employer_id'   => auth()->user()->id,
            'name'          => $request->name,
            'address'       => $request->address,
            'assign_site_manager' => null,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('image')) {
            $imageData = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/sites'), $imageData);
            $input['image'] = $imageData;
        }

        $site = Site::create($input);

        $shiftData = json_decode($request->shift_data, true);

        if (is_array($shiftData) && count($shiftData) > 0) {
            foreach ($shiftData as $shift) {
                Shift::create([
                    'name'        => $shift['shift_name'],
                    'employer_id' => auth()->user()->id,
                    'site_id'     => $site->id,
                    'start_time'  => $shift['shift_start_time'],
                    'end_time'    => $shift['shift_end_time'],
                ]);
            }
        }

        $employee = Employee::find($request->employee_id);
        $visaDetails = VisaDetail::where('employee_id', $request->employee_id)->first();
        if ($employee) {
            $employee->is_promotion = 1;
            $employee->save();
            $siteManager['employer_id'] = auth()->user()->id;
            $siteManager['name'] = $employee->name;
            $siteManager['site_id'] = $site->id;
            $siteManager['email'] = $employee->email;
            $siteManager['number'] = $employee->number;
            $siteManager['password'] = $employee->password;
            $siteManager['address'] = $employee->address;
            $siteManager['image'] = basename($employee->image);
            $siteManager['selfie'] = basename($employee->selfie);
            $siteManager['country'] = $visaDetails->country;
            $siteManager['country_code'] = $visaDetails->country_code;
            $siteManagerPath = public_path('images/site_manager');
            if (!File::exists($siteManagerPath)) {
                File::makeDirectory($siteManagerPath, 0755, true);
            }
            $siteManagerSelfiePath = public_path('images/site_manager/selfie');
            if (!File::exists($siteManagerSelfiePath)) {
                File::makeDirectory($siteManagerSelfiePath, 0755, true);
            }
            $sourcePath = public_path('images/employee/' . $siteManager['image']);
            $destinationPath = public_path('images/site_manager/' . $siteManager['image']);
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $destinationPath);
            }
            $selfieSourcePath = public_path('images/employee/selfie/' . $siteManager['selfie']);
            $selfieDestPath = public_path('images/site_manager/selfie/' . $siteManager['selfie']);
            if (File::exists($selfieSourcePath)) {
                File::copy($selfieSourcePath, $selfieDestPath);
            }
            SiteManager::create($siteManager);
        }
        return $this->success(true, 'Sites created successfully.', $site);
    }


    public function show($id)
    {
        $site = Site::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$site) {
            return $this->success(false, 'Site not found.', 404);
        }

        return $this->success(true, 'Site retrieved successfully.', $site);
    }


    public function update(Request $request, $id)
    {
        $site = Site::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$site) {
            return $this->success(false, 'Site not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                => 'required',
            'address'             => 'required',
            'email'   => ['required', 'email', Rule::unique('sites', 'email')->ignore($site->id)],
            'phone'   => ['required', Rule::unique('sites', 'phone')->ignore($site->id)],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'name'      => $request->name,
            'address'   => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('image')) {
            $oldImagePath = public_path('images/sites/' . $site->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $imageData = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/sites'), $imageData);
            $input['image'] = $imageData;
        }

        $site->update($input);
        return $this->success(true, 'Site updated successfully.', $site);
    }


    public function destroy($id)
    {
        $site = Site::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$site) {
            return $this->error('Site not found.');
        }

        $oldImagePath = public_path('images/sites/' . $site->image);
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }
        Shift::where('site_id', $site->id)->delete();
        $site->delete();

        return $this->success(true, 'Site deleted successfully.');
    }
}
