<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\EmployerAnnouncement;
use App\Models\Notification;
use App\Models\Site;
use App\Models\SiteManager;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmployerAnnouncementController extends Controller
{
    // POST: add-announcement
    public function addAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'      => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $announcement = EmployerAnnouncement::create([
            'employer_id' => auth()->id(),
            'site_id'     => $request->site_id,
            'employee_id' => $request->employee_id,
            'message'     => $request->title,
            'description' => $request->description,
        ]);

        if ($announcement) {
            // $siteIds = Site::where('employer_id', auth()->id())->pluck('id')->toArray();
            // $employees = Employee::whereIn('site_id', $siteIds)->get();

            $siteIds = Site::where('id', $request->site_id)->first();
            $employees = Employee::where('site_id', $siteIds->id)->get();

            if (count($employees) > 0) {
                foreach ($employees as $employee) {
                    $notifications = [
                        'device_token' => $employee->device_token,
                        'device_type'  => $employee->device_type,
                        'title'        =>  $request->title,
                        'message'         =>  $request->description,
                        'type'         => 'add-announcement',
                    ];
                    if ($employee->device_token !== null && $employee->device_token !== '') {
                        $this->sendPushNotification($notifications['device_token'], $notifications['device_type'], $notifications['title'], $notifications['message'], $notifications['type']);
                    }
                    Notification::create([
                        'employer_id' => null,
                        'employee_id' => $employee->id,
                        'type'        => $notifications['type'],
                        'message'     => $notifications['title'],
                        'description' => $notifications['message'],
                    ]);
                }
            }
        }
        return $this->success(true, 'Announcement added successfully.', $announcement);
    }

    public function getEmployerAnnouncement(Request $request)
    {
        $limit = $request->limit;
        $offset = $request->offset;

        $query = EmployerAnnouncement::with(['employee', 'site'])
            ->where('employer_id', auth()->id());
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        if (!empty($request->limit)) {
            $query->skip($offset * $limit)->take($limit);
        }
        $announcements = $query->latest()->get();
        return $this->success(true, 'Announcement retrieved successfully.', $announcements);
    }


    public function getSiteManagerAnnouncement(Request $request)
    {

        $limit = $request->limit;
        $offset = $request->offset;
        $query = EmployerAnnouncement::with(['employee', 'site'])->where('site_id', auth()->user()->site_id)
            ->where(function ($q) {
                $q->where('employer_id', auth()->user()->employer_id)
                    ->whereNull('employee_id');
            });
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        if (!empty($request->limit)){
            $query->skip($offset * $limit)->take($limit);
        }
        $announcements = $query->latest()->get();
        return $this->success(true, 'Announcement retrieved successfully.', $announcements);
    }

    public function getEmployeeAnnouncement(Request $request)
    {

        $limit = $request->limit;
        $offset = $request->offset;
        $siteManager = SiteManager::where('site_id', auth()->user()->site_id)->first();
        $query = EmployerAnnouncement::with(['employee', 'site'])->where('site_id', auth()->user()->site_id)
            ->where('employer_id', $siteManager->employer_id)->orwhere('employee_id', auth()->user()->id);
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        if (!empty($request->limit)){
             $query->skip($offset * $limit)->take($limit);
        }
        $announcements = $query->latest()->get();
        return $this->success(true, 'Announcement retrieved successfully.', $announcements);
    }
}
