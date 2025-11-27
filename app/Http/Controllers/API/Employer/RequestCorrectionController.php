<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\CorrectionRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\SiteManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestCorrectionController extends Controller
{

    public function requestCorrectionList(Request $request)
    {
        $employer = Auth::user();

        $limit = $request->limit;
        $offset = $request->offset;

        $siteManager = SiteManager::where('employer_id', $employer->id)->pluck('id')->toArray();

        $requests = CorrectionRequest::with('siteManager')
            ->whereIn('site_manager_id', $siteManager)
            ->orderBy('created_at', 'DESC')->where('status', 'pending');

        if(!empty($request->limit)){
            $requests = $requests->skip($offset * $limit)->take($limit);
        }
        $requests = $requests->get();

        return $this->success(true, 'Correction request list fetched successfully.', $requests);
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

    public function updateRequestCorrectionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:correction_requests,id',
            'status'     => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $correction = CorrectionRequest::find($request->request_id);

        $correction->status = $request->status;
        $correction->save();

        if ($request->status == 'approved') {

            $siteManager = SiteManager::where('id', $correction->site_manager_id)->first();

            $attendance = Attendance::where('site_manager_id', $siteManager->id)->where('is_site_manager', 1)
                ->whereDate('check_in', date('Y-m-d'))
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->site_manager_id   = $siteManager->id;
                $attendance->site_id       = $siteManager->site_id;
                $attendance->shift_id      = $employee->shift_id ?? 1;
                $attendance->lat           = $request->lat ?? null;
                $attendance->long          = $request->long ?? null;
                $attendance->is_correction = 1;
                $attendance->check_in = date('Y-m-d') . " " . date('H:i:s');
            } else {
                $attendance->check_out = date('Y-m-d') . " " . date('H:i:s');
            }

            $attendance->save();
        }

        return $this->success(true, 'Correction request status updated successfully.');
    }
}
