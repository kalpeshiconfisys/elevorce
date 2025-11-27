<?php

namespace App\Http\Controllers\API\SiteManager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\CorrectionRequest;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestCorrectionController extends Controller
{

    public function requestCorrectionList(Request $request)
    {
        $siteManager = Auth::user();
        $limit = $request->limit;
        $offset = $request->offset;
        $employee = Employee::where('site_id', $siteManager->site_id)->pluck('id')->toArray();
        $requests = CorrectionRequest::with('employee')
            ->whereIn('employee_id', $employee)
            ->orderBy('created_at', 'DESC')->where('status', 'pending');

        if(!empty($request->limit)){
            $requests = $requests->skip($offset * $limit)->take($limit);
        }
        $requests = $requests->get();
        return $this->success(true, 'Correction request list fetched successfully.', $requests);
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

            $employee = Employee::where('id', $correction->employee_id)->first();
            $attendance = Attendance::where('employee_id', $employee->id)->where('is_site_manager', 0)
                ->whereDate('check_in', date('Y-m-d'))
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id   = $employee->id;
                $attendance->site_id       = $employee->site_id;
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

    public function addRequestCorrection(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'text'     => 'required|string|max:500',
            'type'     => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        CorrectionRequest::create([
            'site_manager_id' => auth()->user()->id,
            'site_id'     => auth()->user()->site_id,
            'name'     => auth()->user()->name,
            'check_in'    => $request->date . " " . $request->time,
            'text'        => $request->text,
            'type'        => $request->type,
        ]);

        return $this->success(true, 'Request Correction Add successfully.', []);
    }
}
