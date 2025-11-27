<?php

namespace App\Http\Controllers\admin\Company;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SiteManager;
use App\Models\Site;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Shift;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function index()
    {
        $data = User::paginate(10);
        return view('admin.view.company.index', compact('data'));
    }

    public function dashboard()
    {
        $data = User::get();
        return view('admin.view.company.dashboard', compact('data'));
    }

    public function create()
    {
        $countries = Country::get();
        return view('admin.view.company.create', compact('countries'));
    }

    public function store(Request $request)
    {
        // Check email and number
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->with('error', 'Email already exists.');
        }
        if (User::where('number', $request->number)->exists()) {
            return redirect()->back()->with('error', 'Number already exists.');
        }

        $country = Country::find($request->country_code);

        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'number' => $request->number,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'country_code' => $country ? $country->phone_code : null,
            'country' => $country ? $country->name : null,
        ];

        foreach (['image', 'company_logo', 'company_cover_image'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $imageName = time() . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('images/employer'), $imageName);
                $input[$field] = $imageName;
            }
        }

        $company = User::create($input);

        // Encode ID to Base64
        $encodedId = trim(base64_encode($company->id), '=');

        return redirect(url('admin/company-dashboard/' . $encodedId))->with('success', 'Company created successfully.');
    }

    public function show($id)
    {
        $id = base64_decode($id);
        $company = User::findOrFail($id);
        return view('admin.view.company.show', compact('company'));
    }

    public function edit($id)
    {
        $id = base64_decode($id);

        $employer = User::findOrFail($id);
        return view('admin.view.company.edit', compact('employer'));
    }

    public function update(Request $request, $id)
    {
        $id = base64_decode($id);


        // Check email and number
        if (User::where('email', $request->email)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->with('error', 'Email already exists.');
        }
        if (User::where('number', $request->number)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->with('error', 'Number already exists.');
        }

        $user = User::findOrFail($id);

        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
        ];

        if (!empty($request->password)) {
            $input['password'] = Hash::make($request->password);
        }

        foreach (['image', 'company_logo', 'company_cover_image'] as $field) {
            if ($request->hasFile($field)) {
                if (!empty($user->$field)) {
                    $oldImagePath = public_path('images/employer/' . $user->$field);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
                $file = $request->file($field);
                $imageName = time() . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('images/employer'), $imageName);
                $input[$field] = $imageName;
            }
        }

        $user->update($input);

        $encodedId = trim(base64_encode($user->id), '=');
        return redirect(url('admin/company-dashboard/' . $encodedId))->with('success', 'Company updated successfully.');
    }

    public function destroy($id)
    {
        $id = base64_decode($id);

        $user = User::findOrFail($id);
        $imageFields = ['image', 'company_logo', 'company_cover_image'];

        foreach ($imageFields as $field) {
            if ($user->$field && File::exists(public_path('images/employer/' . $user->$field))) {
                File::delete(public_path('images/employer/' . $user->$field));
            }
        }

        User::destroy($id);
        return redirect(url('admin/dashboard'))->with('success', 'Company deleted successfully.');
    }

    public function companyDashboard($id)
    {
        $id = base64_decode($id);

        $company = User::findOrFail($id);
        $sites = Site::where('employer_id', $id)->pluck("id")->toArray();
        $employees = Employee::whereIn('site_id', $sites)->count();
        $siteManagers = SiteManager::where('employer_id', $id)->count();

        $visasExpiringSoon = \App\Models\VisaDetail::with('employee')
            ->whereNotNull('visa_expiry_date')
            ->where(function ($query) {
                $query->where('visa_expiry_date', '<', now())
                      ->orWhereBetween('visa_expiry_date', [now(), now()->addDays(30)]);
            })
            ->whereHas('employee', function ($query) use ($sites) {
                $query->whereIn('site_id', $sites);
            })
            ->orderBy('visa_expiry_date', 'asc')
            ->limit(5)
            ->get();

        $visaCount = \App\Models\VisaDetail::with('employee')
            ->whereNotNull('visa_expiry_date')
            ->where(function ($query) {
                $query->where('visa_expiry_date', '<', now())
                      ->orWhereBetween('visa_expiry_date', [now(), now()->addDays(30)]);
            })
            ->whereHas('employee', function ($query) use ($sites) {
                $query->whereIn('site_id', $sites);
            })->count();

        $sitesData = Site::where('employer_id', $id)->first();
        $leaveTypes = LeaveType::where('employer_id', $id)->count();
        $shift = Shift::where('employer_id', $id)->count();
        $is_leave_type = $leaveTypes > 0 ? 1 : 0;

        $companies = User::withCount(['site', 'siteManager'])
            ->where('id', $id)
            ->get()
            ->map(function ($i) {
                $sites = Site::where('employer_id', $i->id)->pluck('id')->toArray();
                $employeeCount = Employee::whereIn('site_id', $sites)->count();
                $leaveTypes = LeaveType::where('employer_id', $i->id)->count();
                $lastMonthCount = Employee::whereIn('site_id', $sites)
                    ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                    ->count();
                $growthPercentage = $lastMonthCount > 0 ? round((($employeeCount - $lastMonthCount) / $lastMonthCount) * 100, 2) : 0;

                $i['employee_count'] = $employeeCount;
                $i['leave_type_count'] = $leaveTypes;
                $i['growth_percentage'] = $growthPercentage;

                return $i;
            });

        $data = [
            'company' => $company,
            'employees' => $employees,
            'sites' => count($sites),
            'siteManagers' => $siteManagers,
            'visasExpiringSoon' => $visasExpiringSoon,
            'sitesData' => $sitesData,
            'leaveTypes' => $leaveTypes,
            'shift' => $shift,
            'companies' => $companies,
            'visaCount' => $visaCount,
        ];

        return view('admin.view.company.dashboard', compact('data', 'is_leave_type'));
    }

    public function visaExpiryList($id)
    {
        $id = base64_decode($id);

        $company = User::findOrFail($id);
        $sites = Site::where('employer_id', $id)->pluck("id")->toArray();

        $visasExpiringSoon = \App\Models\VisaDetail::with('employee')
            ->whereNotNull('visa_expiry_date')
            ->where(function ($query) {
                $query->where('visa_expiry_date', '<', now())
                      ->orWhereBetween('visa_expiry_date', [now(), now()->addDays(30)]);
            })
            ->whereHas('employee', function ($query) use ($sites) {
                $query->whereIn('site_id', $sites);
            })
            ->orderBy('visa_expiry_date', 'asc')
            ->paginate(10);

        return view('admin.view.company.visa_expire_list', compact('company', 'visasExpiringSoon'));
    }
}
