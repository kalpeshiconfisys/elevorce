<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\Employee;
use App\Models\Site;
use App\Models\SiteManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{

    public function index()
    {
        $companies = User::withCount(['site', 'siteManager'])
            ->get()
            ->map(function ($i) {
                $siteManagers = SiteManager::where('employer_id', $i->id)->get();
                $sites = Site::where('employer_id', $i->id)->pluck('id')->toArray();
                $employeeCount = Employee::whereIn('site_id', $sites)->count();
                $lastMonthCount = 0;
                $lastMonthCount += Employee::whereIn('site_id', $sites)
                    ->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ])
                    ->count();
                $growthPercentage = $lastMonthCount > 0
                    ? round((($employeeCount - $lastMonthCount) / $lastMonthCount) * 100, 2)
                    : 0;
                $i['employee_count'] = $employeeCount;
                $i['growth_percentage'] = $growthPercentage;

                return $i;
            })
            ->sortByDesc('employee_count')
            ->take(5);

        $user = User::count();
        $site = Site::count();
        $employee = Employee::count();
        $siteManager = SiteManager::count();
        $contactUs = ContactUs::orderBy('id', 'desc')->take(5)->get();
        $totalContactUs = ContactUs::count();
        $data = [
            'user' => $user,
            'sites' => $site,
            'employees' => $employee,
            'managers' => $siteManager,
            'companies' => $companies,
            'contactus' => $contactUs,
            'totalContactUs' => $totalContactUs,
        ];
        return view('admin.view.dashboard', compact('data'));
    }


    public function contactUsList()
    {
        $contactUs = ContactUs::orderBy('id', 'desc')->paginate(10);

        $employer_id = Auth::guard('admin')->user()->id;
        return view('admin.view.contact_view', compact('contactUs', 'employer_id'));
    }
}
