<?php

namespace App\Http\Controllers\API\SiteManager;

use App\Http\Controllers\Controller;
use App\Jobs\EmployeeFaceCheck;
use App\Jobs\SiteManagerFaceCheck;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveSubType;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\SiteManager;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AttendanceManagementController extends Controller
{
    public function getSiteManagerAttendanceManagement(Request $request)
    {


        $siteManager = auth()->user();

        $year  = $request->input('year');
        $month = $request->input('month');

        $limit = $request->limit;
        $offset = $request->offset;

        if (!$year || !$month) {
            return $this->error('Year and month are required.');
        }

        $startDate = Carbon::createFromDate($year, $month, 1);
        $requestedEndDate = $startDate->copy()->endOfMonth();

        $today = Carbon::tomorrow();
        $endDate = $requestedEndDate->greaterThan($today) ? $today : $requestedEndDate;

        $period = CarbonPeriod::create($startDate, $endDate);

        $attendancesQuery = Attendance::with(['site', 'shift'])
            ->where('site_manager_id', $siteManager->id)
            ->whereDate('check_in', '>=', $startDate)
            ->whereDate('check_in', '<=', $endDate);


        if (Schema::hasColumn('attendances', 'is_site_manager')) {
            $attendancesQuery->where('is_site_manager', 1);
        }

        $attendances = $attendancesQuery->get()->keyBy(function ($item) {
            return Carbon::parse($item->check_in)->format('Y-m-d');
        });

        $holidays = Holiday::where('employer_id', $siteManager->employer_id) // adjust relation if needed
            ->get()
            ->map(function ($holiday) {
                return [
                    'start_date' => Carbon::parse($holiday->start_date)->format('Y-m-d'),
                    'end_date'   => Carbon::parse($holiday->end_date)->format('Y-m-d'),
                    'name'       => $holiday->occasion
                ];
            });

        $response = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $record = $attendances->get($dateStr);
            $isWeekend = $date->isSunday();

            $holidayMatch = $holidays->first(function ($h) use ($dateStr) {
                return $dateStr >= $h['start_date'] && $dateStr <= $h['end_date'];
            });

            $isHoliday = !empty($holidayMatch);
            $holidayName = null;


            if ($isHoliday) {
                $isWeekend = false;
                $holidayName = $holidayMatch['name'];
            } elseif ($isWeekend) {
                $holidayName = 'Sunday';
            }



            if ($record) {
                $workedHours = null;
                $overtime = null;
                if ($record->check_in && $record->check_out) {
                    $checkIn  = Carbon::parse($record->check_in);
                    $checkOut = Carbon::parse($record->check_out);
                    $totalMinutes = $checkOut->diffInMinutes($checkIn);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    $workedHours = sprintf('%02d:%02d', $hours, $minutes);
                    if ($record->shift && $record->shift->end_time) {
                        $shiftEndTime = Carbon::parse($record->shift->end_time);
                        $shiftEnd = $shiftEndTime->copy()->setDate(
                            $checkIn->year,
                            $checkIn->month,
                            $checkIn->day
                        );
                        if ($shiftEnd->lessThan($checkIn)) {
                            $shiftEnd->addDay();
                        }
                        if ($checkOut->greaterThan($shiftEnd)) {
                            $otMinutes = $checkOut->diffInMinutes($shiftEnd);
                            $otHours = floor($otMinutes / 60);
                            $otRemMinutes = $otMinutes % 60;
                            $overtime = sprintf('%02d:%02d', $otHours, $otRemMinutes);
                        }
                    }
                }

                $response[] = [
                    'site_manager_id'   => $siteManager->id,
                    'site_manager_name' => $siteManager->name,
                    'site_name'     => optional($siteManager->site)->name,
                    'image'         => $siteManager->image,
                    'date'          => $dateStr,
                    'status'        =>  'Present',
                    'is_request_correction' => false,
                    'check_in'      => $record->check_in,
                    'check_out'     => $record->check_out,
                    'worked_hours'  => $workedHours,
                    'overtime'      => $overtime,
                    'on_leave'      => null,
                    'leave_type'    => null,
                    'time_range'    => null,
                    'half_day_type' => null,
                    'shift_name'    => optional($record->shift)->name,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            } else {

                $response[] = [
                    'site_manager_id'   => $siteManager->id,
                    'site_manager_name' => $siteManager->name,
                    'site_name'     => optional($siteManager->site)->name,
                    'image'         => $siteManager->image,
                    'date'          => $dateStr,
                    'status'        =>  'Absent',
                    'is_request_correction' => true,
                    'check_in'      => null,
                    'check_out'     => null,
                    'worked_hours'  => null,
                    'overtime'      => null,
                    'on_leave'      => null,
                    'leave_type'    => null,
                    'time_range'    => null,
                    'half_day_type' => null,
                    'shift_name'    => null,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            }
        }

        if (!empty($limit)) {
            $total = count($response);
            $offset = (int)($offset ?? 0);
            $limit = (int)($limit ?? 10);

            $start = $total - (($offset + 1) * $limit);


            if ($start >= $total || $start < 0 && ($start + $limit) <= 0) {
                $response = [];
            } else {

                if ($start < 0) {
                    $limit = $limit + $start;
                    $start = 0;
                }
                $response = array_slice($response, $start, $limit);
                $response = array_reverse($response);
            }
        }

        if (!empty($response)) {
            usort($response, function ($a, $b) {
                return strcmp($b['date'], $a['date']);
            });
        }

        return $this->success(true, 'Attendance data retrieved successfully.', $response);
    }

    public function getSiteManagerCurrentMonthAttendanceCount(Request $request)
    {
        $siteManager = auth()->user();

        $year  = (int)now()->format('Y');
        $month = (int)now()->format('m');

        if (!$year || !$month) {
            return $this->error('Year and month are required.');
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startDate->copy()->endOfMonth()->endOfDay();
        $today = Carbon::today()->endOfDay();

        $isCurrentMonth = ($month === (int)now()->format('m') && $year === (int)now()->format('Y'));
        $endDate = $isCurrentMonth ? $today : $endOfMonth;

        $shift = \App\Models\Shift::where('site_id', $siteManager->site_id)->first();

        $presentDays = 0;
        $lateDays = 0;
        $absentDays = 0;
        $halfDays = 0;

        $date = $startDate->copy();

        while ($date->lte($endDate)) {
            $attendance = \App\Models\Attendance::whereDate('check_in', $date->toDateString())
                ->where('site_manager_id', $siteManager->id)
                ->where('is_site_manager', 1)
                ->first();

            if ($attendance && $shift) {
                $shiftStartTime = Carbon::parse($shift->start_time);
                $checkInTime = Carbon::parse($attendance->check_in);

                if ($checkInTime->diffInMinutes($shiftStartTime) > 30) {
                    $lateDays++;
                } else {
                    $presentDays++;
                }
            } else {
                $absentDays++;
            }

            $date->addDay();
        }

        $todayDate = Carbon::today()->toDateString();

        $todayAttendance = \App\Models\Attendance::whereDate('check_in', $todayDate)
            ->where('site_manager_id', $siteManager->id)
            ->where('is_site_manager', 1)
            ->first();

        $yourAttendance = [
            'present_days' => $presentDays,
            'late_days'    => $lateDays,
            'absent_days'  => $absentDays,
            'half_days'    => $halfDays,
            'month'        => $month,
            'year'         => $year,
            'site_id'      => $siteManager->site_id,
            'total_days'   => $presentDays + $lateDays + $absentDays,
            'check_in'     => $todayAttendance ? $todayAttendance->check_in : null,
            'check_out'    => $todayAttendance && $todayAttendance->check_out ? $todayAttendance->check_out : null,

        ];

        // === Get Today's Employee Attendance Summary ===

        $todayDate = Carbon::today()->toDateString();
        $employeeIds = \App\Models\Employee::where('site_id', $siteManager->site_id)->pluck('id');
        $todayAttendance = \App\Models\Attendance::whereDate('check_in', $todayDate)
            ->whereIn('employee_id', $employeeIds)
            ->get();

        $present = 0;
        $late = 0;
        $halfDay = 0;
        $attendedEmployeeIds = [];

        foreach ($todayAttendance as $attendance) {
            $attendedEmployeeIds[] = $attendance->employee_id;
            $empShift = \App\Models\Shift::where('id', $attendance->shift_id)->first();

            if ($empShift) {
                $shiftStartTime = Carbon::parse($empShift->start_time);
                $checkInTime = Carbon::parse($attendance->check_in);

                if ($checkInTime->diffInMinutes($shiftStartTime) > 30) {
                    $late++;
                } else {
                    $present++;
                }
            }
        }

        $absent = count($employeeIds) - count(array_unique($attendedEmployeeIds));
        if ($absent < 0) $absent = 0;

        $todayEmployeeAttendance = [
            'date'            => $todayDate,
            'site_id'         => $siteManager->site_id,
            'total_employees' => count($employeeIds),
            'present'         => $present,
            'late'            => $late,
            'half_day'        => $halfDay,
            'absent'          => $absent,
        ];
        $employee = Employee::where('site_id', $siteManager->site_id)->pluck('id')->toArray();
        $correctionRequest = CorrectionRequest::whereIn('employee_id', $employee)->where('status', 'pending')->count();

        $notificationCount = Notification::where('site_manager_id', $siteManager->id)->where('is_read', 0)->count();

        return $this->success(true, 'Attendance summary retrieved.', [
            'your_attendance'          => $yourAttendance,
            'today_employee_attendance' => $todayEmployeeAttendance,
            'correction_request_count' => $correctionRequest,
            'notification_count'       => $notificationCount
        ]);
    }

    public function getAttendanceListEmployee(Request $request)
    {
        $siteManager = auth()->user();
        $date = $request->input('date');
        $limit = $request->limit;
        $offset = $request->offset;

        if (!$date) {
            return $this->error('Date is required.');
        }

        $dateCarbon = Carbon::parse($date)->startOfDay();
        if ($dateCarbon->greaterThan(Carbon::today())) {
            return $this->success(true, 'Future dates are not allowed.', []);
        }

        $employees = \App\Models\Employee::where('site_id', $siteManager->site_id)->get();
        $response = [];

        foreach ($employees as $employee) {
            // Fetch holiday list for employer
            $holidays = Holiday::where('employer_id', optional($employee->site)->employer_id)
                ->get()
                ->map(function ($holiday) {
                    return [
                        'start_date' => Carbon::parse($holiday->start_date)->format('Y-m-d'),
                        'end_date'   => Carbon::parse($holiday->end_date)->format('Y-m-d'),
                        'name'       => $holiday->occasion
                    ];
                });

            // Check if this date is in a holiday range
            $holidayMatch = $holidays->first(function ($h) use ($dateCarbon) {
                $dateStr = $dateCarbon->format('Y-m-d');
                return $dateStr >= $h['start_date'] && $dateStr <= $h['end_date'];
            });

            $isWeekend = $dateCarbon->isSunday();
            $isHoliday = !empty($holidayMatch);
            $holidayName = $isHoliday
                ? $holidayMatch['name']
                : ($isWeekend ? "Sunday" : null);

            // Check if weekend

            // Fetch attendance
            $attendance = \App\Models\Attendance::with(['site', 'shift'])
                ->where('employee_id', $employee->id)
                ->whereDate('check_in', $dateCarbon)
                ->where(function ($query) {
                    $query->whereNull('site_manager_id')
                        ->orWhere('is_site_manager', 0);
                })
                ->first();

            // Fetch leave
            $leave = \App\Models\Leave::where('employee_id', $employee->id)
                ->whereDate('from_date', '<=', $dateCarbon)
                ->whereDate('to_date', '>=', $dateCarbon)
                ->with('leaveType')
                ->first();

            $onLeave = $leave ? true : false;

            if ($attendance) {
                $workedHours = null;
                $overtime = null;
                if ($attendance->check_in && $attendance->check_out) {
                    $checkIn  = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);
                    $totalMinutes = $checkOut->diffInMinutes($checkIn);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    $workedHours = sprintf('%02d:%02d', $hours, $minutes);

                    if ($attendance->shift && $attendance->shift->end_time) {
                        $shiftEndTime = Carbon::parse($attendance->shift->end_time);
                        $shiftEnd = $shiftEndTime->copy()->setDate(
                            $checkIn->year,
                            $checkIn->month,
                            $checkIn->day
                        );
                        if ($shiftEnd->lessThan($checkIn)) {
                            $shiftEnd->addDay();
                        }
                        if ($checkOut->greaterThan($shiftEnd)) {
                            $otMinutes = $checkOut->diffInMinutes($shiftEnd);
                            $otHours = floor($otMinutes / 60);
                            $otRemMinutes = $otMinutes % 60;
                            $overtime = sprintf('%02d:%02d', $otHours, $otRemMinutes);
                        }
                    }
                }

                $status = 'Present';
                $isRequestCorrection = false;
                if ($leave && $leave->time_range === 'half') {
                    $leaveSubType  = LeaveSubType::where('id', $leave->leave_sub_type_id)->first();
                    $status = $leaveSubType->name;
                    $isRequestCorrection = true;
                }
                if ($leave && $leave->time_range === 'early') {
                    $leaveSubType  = LeaveSubType::where('id', $leave->leave_sub_type_id)->first();
                    $status = $leaveSubType->name;
                    $isRequestCorrection = true;
                }
                $response[] = [
                    'employee_id'   => $employee->id,
                    'employee_name' => $employee->name,
                    'site_name'     => optional($attendance->site)->name,
                    'address'       => $employee->address,
                    'image'         => $employee->image,
                    'date'          => $dateCarbon->toDateString(),
                    'status'        => $status,
                    'is_request_correction' => $isRequestCorrection,
                    'check_in'      => $attendance->check_in,
                    'check_out'     => $attendance->check_out,
                    'worked_hours'  => $workedHours,
                    'overtime'      => $overtime,
                    'on_leave'      => $onLeave,
                    'leave_type'    => $leave?->leaveType?->name,
                    'time_range'    => $leave?->time_range,
                    'half_day_type' => $leave?->half_day_type,
                    'shift_name'    => optional($attendance->shift)->name,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            } else {
                $status = 'Absent';
                $isRequestCorrection = false;
                if ($onLeave) {
                    if ($leave?->time_range === 'half') {
                        $leaveSubType  = LeaveSubType::where('id', $leave->leave_sub_type_id)->first();
                        $status = $leaveSubType->name;
                        $isRequestCorrection = true;
                    } else if ($leave && $leave->time_range === 'early') {
                        $leaveSubType  = LeaveSubType::where('id', $leave->leave_sub_type_id)->first();
                        $status = $leaveSubType->name;
                        $isRequestCorrection = true;
                    } else {
                        $status = 'Absent';
                        $isRequestCorrection = false;
                    }
                } else {
                    $status = 'Miss Punch';
                    $isRequestCorrection = true;
                }
                $response[] = [
                    'employee_id'   => $employee->id,
                    'employee_name' => $employee->name,
                    'site_name'     => null,
                    'address'       => $employee->address,
                    'image'         => $employee->image,
                    'date'          => $dateCarbon->toDateString(),
                    'status'        => $status,
                    'is_request_correction' => $isRequestCorrection,
                    'check_in'      => null,
                    'check_out'     => null,
                    'worked_hours'  => null,
                    'overtime'      => null,
                    'on_leave'      => $onLeave,
                    'leave_type'    => $leave?->leaveType?->name,
                    'time_range'    => $leave?->time_range,
                    'half_day_type' => $leave?->half_day_type,
                    'shift_name'    => null,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            }
        }
        if (!empty($limit)) {
            $total = count($response);
            $offset = (int)($offset ?? 0);
            $limit = (int)($limit ?? 10);

            $start = $total - (($offset + 1) * $limit);


            if ($start >= $total || $start < 0 && ($start + $limit) <= 0) {
                $response = [];
            } else {

                if ($start < 0) {
                    $limit = $limit + $start;
                    $start = 0;
                }
                $response = array_slice($response, $start, $limit);
                $response = array_reverse($response);
            }
        }

        return $this->success(true, 'Attendance list for all employees retrieved successfully.', $response);
    }

    public function checkInEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        if ($request->is_sitemanager == true) {
            $siteManager   = SiteManager::find($request->employee_id);
            if (!$siteManager) {
                return $this->error('Site Manager not found.');
            }
            $siteId     = $siteManager->site_id;
            $shiftId    = optional(Shift::where('site_id', $siteManager->site_id)->orderBy('id', 'asc')->first())->id;

            $today      = date('Y-m-d');

            $attendance = Attendance::where('site_manager_id', $siteManager->id)->where('is_site_manager', 1)
                ->whereDate('check_in', $today)
                ->first();

            if ($attendance) {
                if ($attendance->check_out) {
                    return $this->error("You have already Checked In and Checked Out today. No further entries allowed.");
                }
                $attendance->update([
                    'check_out' => now(),
                ]);
                $status = "Check Out";
            } else {
                $attendance = Attendance::create([
                    'employee_id' => null,
                    'site_manager_id' => $siteManager->id,
                    'is_site_manager' => 1,
                    'site_id'     => $siteId,
                    'shift_id'    => $shiftId,
                    'check_in'    => now(),
                    'lat'    => $request->lat,
                    'long'    =>  $request->long,
                ]);
                $status = "Check In";
            }
            $attendance['title'] = $status;
            $attendance['message'] = "$status successfully.";
            $attendance['name'] = $siteManager->name;
            $attendance['image'] = $siteManager->image;
        } else {
            $employee   = Employee::find($request->employee_id);
            if (!$employee) {
                return $this->error('Employee not found.');
            }
            $siteId     = $employee->site_id;
            $shiftId    = $employee->shift_id;
            $today      = date('Y-m-d');

            $attendance = Attendance::where('employee_id', $employee->id)->where('is_site_manager', 0)
                ->whereDate('check_in', $today)
                ->first();

            if ($attendance) {

                if ($attendance->check_out) {
                    return $this->error("You have already Checked In and Checked Out today. No further entries allowed.");
                }
                $attendance->update([
                    'check_out' => now(),
                ]);
                $status = "Check Out";
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'site_id'     => $siteId,
                    'shift_id'    => $shiftId,
                    'check_in'    => now(),
                    'lat'    => $request->lat,
                    'long'    =>  $request->long,
                ]);
                $status = "Check In";
            }
            $attendance['title'] = $status;
            $attendance['message'] = "$status successfully.";
            $attendance['name'] = $employee->name;
            $attendance['image'] = $employee->image;
        }
        $attendance['is_sitemanager'] = $request->is_sitemanager;
        return $this->success(true, "$status  successfully.", $attendance);
    }

    public function checkInSiteManager(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:check-in,check-out',
            'selfie' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $job = new SiteManagerFaceCheck();
        $output = $job->handle($request->selfie);

        if ($output['status'] == 'success') {

            $employeeId = $request->site_manager_id;
            $employee = SiteManager::find($employeeId);
            $siteId     = $employee->site_id;
            $checkIn    = date('Y-m-d H:i:s');

            if ($request->type == 'check-in') {
                $shiftId = $this->getShiftForCheckIn($siteId, $checkIn);
                if (!$shiftId) {
                    return $this->error('No shift matched for this check-in time.');
                }
                $attendance = Attendance::create([
                    'employee_id' => $employeeId,
                    'site_id'     => $siteId,
                    'shift_id'    => $shiftId,
                    'check_in'    => date('Y-m-d H:i:s'),
                ]);
                return $this->success(true, 'Check-in recorded with shift.', $attendance);
            }

            if ($request->type == 'check-out') {

                $attendance = Attendance::where('employee_id', $employeeId)
                    ->whereDate('check_in', now()->toDateString())
                    ->first();

                if (!$attendance) {
                    return $this->error('Check-in not found.');
                }

                $attendance->update([
                    'check_out' => date('Y-m-d H:i:s')
                ]);

                return $this->success(true, 'Check-Out recorded with shift.', $attendance);
            }
        } else {
            return $this->error('Your face is not recognized.');
        }
    }
}
