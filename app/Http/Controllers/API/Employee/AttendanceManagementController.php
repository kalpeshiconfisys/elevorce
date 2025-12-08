<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Leave;
use App\Models\LeaveSubType;
use App\Models\Notification;

class AttendanceManagementController extends Controller
{

    public function getAttendanceManagement(Request $request)
    {
        $employee = auth()->user();

        $year  = $request->input('year');
        $month = $request->input('month');


        $limit = $request->limit;
        $offset = $request->offset;


        if (!$year || !$month) {
            return $this->error('Year and month are required.');
        }

        // try {

        $startDate = Carbon::createFromDate($year, $month, 1);
        $requestedEndDate = $startDate->copy()->endOfMonth();

        $today = Carbon::tomorrow();
        $endDate = $requestedEndDate->greaterThan($today) ? $today : $requestedEndDate;

        $period = CarbonPeriod::create($startDate, $endDate);

        $attendancesQuery = Attendance::with(['site', 'shift'])
            ->where('employee_id', $employee->id)
            ->whereDate('check_in', '>=', $startDate)
            ->whereDate('check_in', '<=', $endDate);

        if (Schema::hasColumn('attendances', 'site_manager_id')) {
            $attendancesQuery->whereNull('site_manager_id');
        }
        if (Schema::hasColumn('attendances', 'is_site_manager')) {
            $attendancesQuery->where('is_site_manager', 0);
        }

        $attendances = $attendancesQuery->get()->keyBy(function ($item) {
            return Carbon::parse($item->check_in)->format('Y-m-d');
        });

        $holidays = Holiday::where('employer_id', optional($employee->site)->employer_id) // adjust relation if needed
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
            // Decide priority: if holiday exists, holiday overrides weekend
            if ($isHoliday) {
                $isWeekend = false;
                $holidayName = $holidayMatch['name'];
            } elseif ($isWeekend) {
                $holidayName = 'Sunday';
            }
            $leave = Leave::where('employee_id', $employee->id)
                // ->where('status', 'approved')
                ->whereDate('from_date', '<=', $dateStr)
                ->whereDate('to_date', '>=', $dateStr)
                ->with('leaveType')
                ->first();

            $onLeave = $leave ? true : false;
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
                    'site_name'     => optional($record->site)->name,
                    'image'         => $record->image,
                    'date'          => $dateStr,
                    'status'        => $status,
                    'is_request_correction' => $isRequestCorrection,
                    'check_in'      => $record->check_in,
                    'check_out'     => $record->check_out,
                    'worked_hours'  => $workedHours,
                    'overtime'      => $overtime,
                    'on_leave'      => $onLeave,
                    'leave_type'    => $leave?->leaveType?->name,
                    'time_range'    => $leave?->time_range,
                    'half_day_type' => $leave?->half_day_type,
                    'shift_name'    => optional($record->shift)->name,
                    'is_weekend'    => $isWeekend,
                    'is_holiday'    => $isHoliday,
                    'holiday_name'  => $holidayName,
                ];
            } else {

                $status = 'Absent';

                $isRequestCorrection = false;
                if ($leave) {

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
                    'image'         => null,
                    'date'          => $dateStr,
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

        if (!empty($response)) {
            usort($response, function ($a, $b) {
                return strcmp($b['date'], $a['date']);
            });
        }


        return $this->success(true, 'Attendance data retrieved successfully.', $response);
        // } catch (\Exception $e) {
        //     return $this->error('An error occurred while fetching attendance data.');
        // }
    }

    public function getCurrentMonthAttendanceCount(Request $request)
    {
        $employee = auth()->user();

        $year  = $request->input('year');
        $month = $request->input('month');

        if (!$year || !$month) {
            return $this->error('Year and month are required.', [], 422);
        }

        try {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endOfMonth = $startDate->copy()->endOfMonth()->endOfDay();
            $today = Carbon::today()->endOfDay();
            $isCurrentMonth = ($month == now()->month && $year == now()->year);
            $endDate = $isCurrentMonth ? $today : $endOfMonth;

            $shift = \App\Models\Shift::where('site_id', $employee->site_id)->first(); // Assume 1 shift for site

            $presentDays = 0;
            $halfDays = 0;
            $lateDays = 0;
            $absentDays = 0;

            $dailyDates = [];
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                $dailyDates[] = $date->toDateString();
                $date->addDay();
            }

            $attendances = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereBetween('check_in', [$startDate, $endDate])
                ->get()
                ->groupBy(fn($att) => Carbon::parse($att->check_in)->toDateString());

            foreach ($dailyDates as $dateStr) {
                if (isset($attendances[$dateStr])) {
                    $attendance = $attendances[$dateStr]->first();

                    if ($shift) {
                        $shiftStart = Carbon::parse($shift->start_time);
                        $checkInTime = Carbon::parse($attendance->check_in);

                        if ($checkInTime->diffInMinutes($shiftStart) > 30) {
                            $lateDays++;
                        } else {
                            $presentDays++;
                        }
                    } else {
                        $presentDays++;
                    }
                } else {
                    $absentDays++;
                }
            }
            $halfDays = Leave::where('employee_id', $employee->id)
                ->whereNotNull('half_day_type')
                ->whereMonth('from_date', $month)->whereyear('from_date', $year)
                ->count();


            $todayDate = Carbon::today()->toDateString();
            $todayAttendance = $attendances[$todayDate][0] ?? null;

            $notificationCount = Notification::where('employee_id', $employee->id)->where('is_read', 0)->count();

            $response = [
                'present_days' => $presentDays,
                'half_days'    => $halfDays,
                'late_days'    => $lateDays,
                'absent_days'  => $absentDays,
                'month'        => $month,
                'site_id'      => $employee->site_id,
                'check_in'     => $todayAttendance ? $todayAttendance->check_in : null,
                'check_out'    => $todayAttendance && $todayAttendance->check_out ? $todayAttendance->check_out : null,
                'notification_count' => $notificationCount,
            ];

            return $this->success(true, 'Attendance count fetched successfully.', $response);
        } catch (\Exception $e) {
            return $this->error('Error fetching attendance count.');
        }
    }
}
