<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveSubType;
use App\Models\Notification;
use App\Models\Site;
use App\Models\SiteManager;
use Carbon\Carbon;

class AttendanceManagementController extends Controller
{
    public function getAttendanceList(Request $request)
    {

        $employer = auth()->user();
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
        $employees = SiteManager::where('employer_id', $employer->id)->get();
        $response = [];
        foreach ($employees as $employee) {
            $holidays = Holiday::where('employer_id', $employee->employer_id)
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

            $attendance = Attendance::with(['site', 'shift'])
                ->where('site_manager_id', $employee->id)
                ->whereDate('check_in', $dateCarbon)
                ->first();

            if ($attendance) {
                +$workedHours = null;
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

                $response[] = [
                    'site_manager_id'   => $employee->id,
                    'site_manager_name' => $employee->name,
                    'site_name'     => optional($attendance->site)->name,
                    'address'       => $employee->address,
                    'image'         => $employee->image,
                    'date'          => $dateCarbon->toDateString(),
                    'status'        => 'Present',
                    'is_request_correction' => false,
                    'check_in'      => $attendance->check_in,
                    'check_out'     => $attendance->check_out,
                    'worked_hours'  => $workedHours,
                    'overtime'      => $overtime,
                    'on_leave'      => null,
                    'leave_type'    => null,
                    'time_range'    => null,
                    'half_day_type' => null,
                    'shift_name'    => optional($attendance->shift)->name,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            } else {

                $response[] = [
                    'site_manager_id'   => $employee->id,
                    'site_manager_name' => $employee->name,
                    'site_name'     => null,
                    'address'       => $employee->address,
                    'image'         => $employee->image,
                    'date'          => $dateCarbon->toDateString(),
                    'status'        => 'Absent',
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
        return $this->success(true, 'Attendance list for all site manager retrieved successfully.', $response);
    }

    public function getSiteManagerTodayAttendanceCount(Request $request)
    {
        $employer = auth()->user();
        $todayDate = Carbon::today()->toDateString();
        $ids = \App\Models\SiteManager::where('employer_id', $employer->id)->pluck('id');
        $todayAttendance = \App\Models\Attendance::whereDate('check_in', $todayDate)
            ->whereIn('site_manager_id', $ids)
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

        $absent = count($ids) - count(array_unique($attendedEmployeeIds));
        if ($absent < 0) $absent = 0;

        $todayEmployeeAttendance['sitemanager-today-attendance-count'] = [
            'date'            => $todayDate,
            'total_site_manager' => count($ids),
            'present'         => $present,
            'late'            => $late,
            'half_day'        => $halfDay,
            'absent'          => $absent,
        ];

        $totalsitesData = Site::where('employer_id', $employer->id)->pluck('id')->toArray();
        $totalEmployees = Employee::whereIn('site_id', $totalsitesData)->count();

        $oneMonthFromNow = Carbon::now()->addMonth();

        $expiringEmployeesCount = Employee::with('visa_details')
            ->where('site_id', $totalsitesData)
            ->whereHas('visa_details', function ($query) use ($oneMonthFromNow) {
                $query->whereDate('visa_expiry_date', '<=', $oneMonthFromNow);
            })
            ->count();

        $siteManager = SiteManager::where('employer_id', $employer->id)->pluck('id')->toArray();

        $correctionCount = CorrectionRequest::whereIn('site_manager_id', $siteManager)->where('status', 'pending')->count();

        $notificationCount = Notification::where('employer_id', $employer->id)->where('is_read', 0)->count();

        $todayEmployeeAttendance['count-details'] = [
            'employee_count' => $totalEmployees,
            'site_count' => count($totalsitesData),
            'visa_expiry_count' => $expiringEmployeesCount,
            'total_correction_request_count' => $correctionCount,
            'notification_count' => $notificationCount,
        ];
        return $this->success(true, 'Dashboard retrieved successfully.', $todayEmployeeAttendance);
    }

    public function employeeVisaExpiryList(Request $request)
    {
        $employer = auth()->user();
        $limit = $request->limit ;
        $offset = $request->offset;
        $oneMonthFromNow = Carbon::now()->addMonth();
        $sites = Site::where('employer_id', $employer->id)->pluck('id')->toArray();
        $expiringEmployees = Employee::with('visa_details')
            ->whereIn('site_id', $sites)
            ->whereHas('visa_details', function ($query) use ($oneMonthFromNow) {
                $query->whereDate('visa_expiry_date', '<=', $oneMonthFromNow);
            });

        if(!empty($request->limit)){
            $expiringEmployees = $expiringEmployees->skip($offset * $limit)->take($limit);
        }

        $expiringEmployees = $expiringEmployees->get();

        return $this->success(true, 'Visa Expiry List retrieved successfully.', $expiringEmployees);
    }
}
