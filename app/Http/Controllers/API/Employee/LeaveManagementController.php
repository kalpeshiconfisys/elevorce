<?php


namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Leave;
use App\Models\LeaveSubType;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Models\ReplacementShift;
use App\Models\Shift;
use App\Models\Site;
use App\Models\SiteManager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LeaveManagementController extends Controller
{
    public function getLeaves(Request $request)
    {
        $limit = $request->limit;
        $offset = $request->offset;
        $query = Leave::with(['leaveType', 'leaveSubType'])
            ->where('employee_id', auth()->user()->id)->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== null) {
            $query->where('status', $request->status); // e.g., pending, approved, rejected
        }

        if(!empty($request->limit)){
            $query->skip($offset * $limit)->take($limit);
        }

        $leaves = $query->get();

        return $this->success(true, 'Leaves fetched successfully.', $leaves);
    }

    public function addLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_type_id'    => 'required',
            'leave_reason'     => 'nullable',
            'leave_status'     => 'nullable',
            'from_date'        => 'required|date_format:d-m-Y',
            'to_date'          => 'required|date_format:d-m-Y',
            'time_range'       => 'nullable|in:half,full',
            'report_sickness'  => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employee_id'     => auth()->user()->id,
            'leave_type_id'   => $request->leave_type_id,
            'leave_reason'    => $request->leave_reason,
            'leave_status'    => $request->leave_status ?? 'unpaid',
            'from_date'       => \Carbon\Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d'),
            'to_date'         => \Carbon\Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d'),
            'is_emergency'    => $request->is_emergency,
            'shift_id'        => auth()->user()->shift_id,
        ];

        if ($request->leave_sub_type_id == 1) {
            $input['time_range'] = 'full';
            $input['leave_sub_type_id'] = 1;
        } else if ($request->leave_sub_type_id == 2) {
            $input['time_range'] = 'half';
            $input['half_day_type'] = 'morning';
            $input['leave_sub_type_id'] = 2;
        } else if ($request->leave_sub_type_id ==  3) {
            $input['time_range'] = 'half';
            $input['half_day_type'] = 'evening';
            $input['leave_sub_type_id'] = 3;
        } elseif ($request->leave_sub_type_id == 4) {
            $input['time_range'] = 'early';
            $input['leave_sub_type_id'] = 4;
        }

        if ($request->hasFile('report_sickness')) {
            $rand = rand(111111111, 999999999);
            $fileName = time() . $rand . '.' . $request->report_sickness->extension();
            $request->report_sickness->move(public_path('images/leave/report_sickness/'), $fileName);
            $input['report_sickness'] = $fileName;
        }
        $leave = Leave::create($input);
        if ($leave) {
            $employee = auth()->user();
            $site = Site::find($employee->site_id);
            if ($site) {
                $siteManager = SiteManager::where('site_id', $site->id)->first();
                if ($siteManager && !empty($siteManager->device_token)) {
                    $notifications = [
                        'device_token' => $siteManager->device_token,
                        'device_type'  => $siteManager->device_type,
                        'title'        => 'Leave Request Submitted',
                        'message'         =>  $employee->name . " has requested leave from " .
                            date('d-m-Y', strtotime($leave->from_date)) .
                            " to " . date('d-m-Y', strtotime($leave->to_date)),
                        'type'         => 'leave-request',
                    ];
                    $this->sendPushNotification($notifications['device_token'], $notifications['device_type'], $notifications['title'], $notifications['message'], $notifications['type']);
                    Notification::create([
                        'employer_id' => null,
                        'site_manager_id' => $siteManager->id,
                        'employee_id' => null,
                        'type'        => $notifications['type'],
                        'message'     => $notifications['title'],
                        'description' => $notifications['message'],
                    ]);
                }
            }
        }
        return $this->success(true, 'Leave request submitted successfully.', $leave);
    }

    public function cancelLeave($id)
    {
        $leave = Leave::where('id', $id)->first();

        if (!$leave) {
            return $this->error('Leave not found or cannot be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        if ($leave) {
            $employee = auth()->user();
            $site = Site::find($employee->site_id);

            if ($site) {
                $siteManager = SiteManager::where('site_id', $site->id)->first();

                if ($siteManager && !empty($siteManager->device_token)) {
                    $notifications = [
                        'device_token' => $siteManager->device_token,
                        'device_type'  => $siteManager->device_type,
                        'title'        => 'Leave Request Cancelled',
                        'body'         => $employee->name . " has cancelled leave from " .
                            date('d-m-Y', strtotime($leave->from_date)) .
                            " to " . date('d-m-Y', strtotime($leave->to_date)),
                        'type'         => 'leave-cancel',
                    ];

                    // Push Notification moklvo
                    $this->sendPushNotification(
                        $notifications['device_token'],
                        $notifications['device_type'],
                        $notifications['title'],
                        $notifications['body'],
                        $notifications['type']
                    );

                    // DB ma notification save karvo
                    Notification::create([
                        'employer_id'     => null,
                        'site_manager_id' => $siteManager->id,
                        'employee_id'     => null,
                        'type'            => $notifications['type'],
                        'message'         => $notifications['title'],
                        'description'     => $notifications['body'],
                    ]);
                }
            }
        }

        return $this->success(true, 'Leave cancelled successfully.', $leave);
    }


    public function leaveType()
    {
        $employee = auth()->user();
        $site = Site::where('id', $employee->site_id)->first();
        $leaveTypes = LeaveType::where('employer_id', $site->employer_id)->get();
        $leaveSubTypes = LeaveSubType::get();
        $response['leave_type'] =  $leaveTypes;
        $response['leave_sub_type'] =  $leaveSubTypes;
        return $this->success(true, 'Leave types fetched successfully.', $response);
    }

    public function leaveUsed()
    {
        $employee = auth()->user();

        $site = Site::find($employee->site_id);
        if (!$site) {
            return $this->error('Site not found.');
        }
        $leaveTypes = LeaveType::where('employer_id', $site->employer_id)->get();
        $response = [];
        foreach ($leaveTypes as $type) {
            $usedLeaves = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->sum(DB::raw("DATEDIFF(to_date, from_date) + 1"));
            $response[] = [
                'leave_type_id'    => $type->id,
                'leave_type_name'  => $type->leave_name,
                'total_leaves'     => $type->count,
                'used_leaves'      => intval($usedLeaves),
                'remaining_leaves' => max(0, $type->count - $usedLeaves),
            ];
        }
        return $this->success(true, 'Leave usage data fetched successfully.', $response);
    }

    public function leaveSubType()
    {
        $leaveTypes = LeaveSubType::get();
        return $this->success(true, 'Leave type time data fetched successfully.', $leaveTypes);
    }

    function getShiftForCheckIn($siteId, $checkInTime)
    {
        $shifts = Shift::where('site_id', $siteId)->get();
        $checkIn = Carbon::parse($checkInTime);

        foreach ($shifts as $shift) {
            $shiftStart = Carbon::parse($shift->start_time);
            $shiftEnd = Carbon::parse($shift->end_time);


            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }

            $startWithDate = $checkIn->copy()->setTimeFrom($shiftStart);
            $endWithDate = $checkIn->copy()->setTimeFrom($shiftEnd);
            if ($endWithDate->lt($startWithDate)) {
                $endWithDate->addDay();
            }

            if ($checkIn->between($startWithDate->copy()->subHour(), $endWithDate->copy()->addHour())) {
                return $shift->id;
            }
        }

        return null;
    }

    public function emergencyLeaveAccept(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'leave_id'     => 'required|exists:leaves,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $leave = Leave::where('id', $request->leave_id)
            ->where('is_emergency', 1)
            ->first();

        if (!$leave) {
            return $this->error("Emergency leave not found ");
        }


        $employee = Employee::find($leave->employee_id);

        $siteId = $employee->site_id;

        $shiftId = Shift::where('id', $employee->shift_id)->first();

        if (!$shiftId) {
            return $this->error('No shift matched for this check-in time.');
        }

        $alreadyAccepted = ReplacementShift::where('date', date('Y-m-d'))->where('shift_id', $shiftId)
        ->where('site_id', $employee->site_id)
        ->where('employee_id', $employee->id)
        ->exists();

        if ($alreadyAccepted) {
            return $this->error("This emergency leave is already accepted by someone else.");
        }

        $replacement = ReplacementShift::create([
            'site_id'      => $siteId,
            'employee_id'  => $employee->id,
            'shift_id'     => $shiftId->shift_id,
            'note'         => $request->note ?? 'Accepted Leave Request From Emergemcy',
            'date'         => date('Y-m-d'),
        ]);

        return $this->success(true, 'Emergency leave covered successfully.', $replacement);
    }
}
