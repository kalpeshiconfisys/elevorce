<?php

namespace App\Http\Controllers\API\SiteManager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Notification;
use App\Models\ReplacementShift;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveManagementController extends Controller
{

    public function getLeaveRequest(Request $request)
    {
        $siteManager = Auth::user();
        $limit = $request->limit;
        $offset = $request->offset;
        $leaveStatus = $request->query('status');
        $employeeId = $request->query('employee_id');
        $is_emergency = $request->query('is_emergency');

        $employee = Employee::where('site_id', $siteManager->site_id)
            ->pluck('id')
            ->toArray();

        $query = Leave::query();

        if ($leaveStatus) {
            $query->where('status', $leaveStatus);
        }

        if (!is_null($is_emergency)) {
            $query->where('is_emergency', $is_emergency);
        }


        $leaves = $query->with(['leaveType', 'employee', 'leaveSubType'])
            ->whereIn('employee_id', $employee)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($i) {

                $shift = Shift::where('id', $i->shift_id)->first();
                $replaceshift = ReplacementShift::where('leave_id', $i->id)->first();

                $i['is_assign'] = 0;
                $i['is_assign_out'] = 0;

                if ($replaceshift) {
                    $i['is_assign'] = 1;
                }

                $today = now()->format('Y-m-d');
                $currentTime = now()->format('H:i:s');

                if ($shift) {
                    if ($i->from_date < $today) {
                        $i['is_assign_out'] = 1;
                    } elseif ($i->from_date == $today && $shift->end_time <= $currentTime) {
                        $i['is_assign_out'] = 1;
                    }
                }

                return $i;
            });

        if(!empty($request->limit)){
            $leaves = $leaves->skip($offset * $limit)->take($limit)->values();
        }



        return $this->success(true, 'Leave requests fetched successfully.', $leaves);
    }

    public function getEmergencyLeaveRequest(Request $request)
    {
        $siteManager = Auth::user();
        $limit = $request->limit;
        $offset = $request->offset;
        $leaveStatus = $request->query('status');
        $employeeId = $request->query('employee_id');
        $employee = Employee::where('site_id', $siteManager->site_id)->pluck('id')->toArray();

        $query = Leave::query();

        if ($leaveStatus) {
            $query->where('status', $leaveStatus);
        }

        $leaves = $query->with(['leaveType', 'employee', 'leaveSubType'])->whereIn('employee_id', $employee)->where('is_emergency', 1)->orderBy('created_at', 'desc');

        if(!empty($request->limit)){
           $leaves = $leaves->skip($offset * $limit)->take($limit);
        }

        $leaves = $leaves->get();
        return $this->success(true, 'Leave requests fetched successfully.', $leaves);
    }

    public function leaveStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_id'     => 'required',
            'status' => 'required|in:approved,rejected,pending',
            'rejected_reason' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $leave = Leave::find($request->leave_id);
        $leave->status = $request->status;
        if ($request->status === 'rejected') {
            $leave->rejected_reason = $request->rejected_reason ?? null;
        } else {
            $leave->rejected_reason = null;
        }
        $leave->save();

        if ($leave) {
            $employee = Employee::where('id', $leave->employee_id)->first();
            if ($employee && !empty($employee->device_token)) {
                $notifications = [
                    'device_token' => $employee->device_token,
                    'device_type'  => $employee->device_type,
                    'title'        => 'Leave Status Update',
                    'message'      => 'Your leave request has been ' . ucfirst($request->status) . '. Reason: ' . $request->rejected_reason,
                    'type'         => 'leave-status-update',
                ];
                $this->sendPushNotification($notifications['device_token'], $notifications['device_type'], $notifications['title'], $notifications['message'], $notifications['type']);
                Notification::create([
                    'employer_id' => null,
                    'site_manager_id' => null,
                    'employee_id' => $employee->id,
                    'type'        => $notifications['type'],
                    'message'     => $notifications['title'],
                    'description' => $notifications['message'],
                ]);
            }
            // $employee = Employee::where('site_id', auth()->user()->site_id)->get();
            // if ($request->status === 'approved' && $leave->is_emergency == 1) {
            //     $message = "Your *Emergency Leave* request for date " . $leave->date .
            //    " has been " . ucfirst($request->status) .
            //    ($request->rejected_reason ? ". Note: " . $request->rejected_reason : ".");

            //     $notifications = [
            //         'device_token' => $employee->device_token,
            //         'device_type'  => $employee->device_type,
            //         'title'        => 'Leave Status Update',
            //         'message'      =>  $message,
            //         'type'         => 'leave-status-update',
            //         'leave_id'         => $leave->id,
            //     ];
            //     $this->sendPushNotification($notifications['device_token'], $notifications['device_type'], $notifications['title'], $notifications['message'], $notifications['type'],$notifications['leave_id']);
            //     Notification::create([
            //         'employer_id' => null,
            //         'site_manager_id' => null,
            //         'employee_id' => $employee->id,
            //         'type'        => $notifications['type'],
            //         'message'     => $notifications['title'],
            //         'description' => $notifications['message'],
            //         'leave_id' => $notifications['leave_id'],
            //     ]);
            // }
        }

        return $this->success(true, 'Leave status updated successfully.', $leave);
    }
}
