<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Shift;
use App\Models\SiteManager;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::where('employer_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return $this->success(true, 'Shifts retrieved successfully.', $shifts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'site_id'    =>  'required|exists:sites,id',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'status'     => 'in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $shift = Shift::create([
            'name'        => $request->name,
            'employer_id' => auth()->user()->id,
            'site_id'     => $request->site_id,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'status'      => $request->status ?? 'active',
        ]);



        return $this->success(true, 'Shift created successfully.', $shift);
    }

    public function show($id)
    {
        $shift = Shift::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$shift) {
            return $this->success(false, 'Shift not found.', 404);
        }

        return $this->success(true, 'Shift retrieved successfully.', $shift);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$shift) {
            return $this->success(false, 'Shift not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'site_id'    => 'required|integer',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'status'     => 'in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $shift->update([
            'name'       => $request->name,
            'site_id'    => $request->site_id,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'status'     => $request->status ?? $shift->status,
        ]);

        return $this->success(true, 'Shift updated successfully.', $shift);
    }

    public function destroy($id)
    {
        $shift = Shift::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$shift) {
            return $this->success(false, 'Shift not found.', 404);
        }

        $shift->delete();

        return $this->success(true, 'Shift deleted successfully.');
    }


    public function getShiftList()
    {
        $employee = Employee::where('id', auth()->user()->id)->first();
        if ($employee) {
            $shifts = Shift::where('id', $employee->shift_id)->first();
        }
        return $this->success(true, 'Shifts retrieved successfully.', $shifts);
    }


    public function getShiftListSiteManager()
    {
        $siteManager = SiteManager::where('id', auth()->user()->id)->first();
        $shifts = Shift::where('employer_id', $siteManager->employer_id)->where('site_id', $siteManager->site_id)->orderBy('created_at', 'desc')->get();
        return $this->success(true, 'Shifts retrieved successfully.', $shifts);
    }
}
