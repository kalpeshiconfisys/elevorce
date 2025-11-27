<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $leaveTypes = LeaveType::where('employer_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return $this->success(true, 'Leave types retrieved successfully.', $leaveTypes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_name' => 'required',
            'count'      => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employer_id' => auth()->user()->id,
            'leave_name'  => $request->leave_name,
            'count'       => $request->count,
        ];

        $leaveType = LeaveType::create($input);

        return $this->success(true, 'Leave type created successfully.', $leaveType);
    }

    public function show($id)
    {
        $leaveType = LeaveType::where('id', $id)
                              ->where('employer_id', auth()->user()->id)
                              ->first();
        if (!$leaveType) {
            return $this->error('Leave type not found.');
        }
        return $this->success(true, 'Leave type retrieved successfully.', $leaveType);
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::where('id', $id)->first();

        if (!$leaveType) {
             return $this->error('Leave type not found.');
        }

        $validator = Validator::make($request->all(), [
            'leave_name' => 'required',
            'count'      => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $leaveType->update([
            'leave_name'  => $request->leave_name,
            'count'       => $request->count,
        ]);

        return $this->success(true, 'Leave type updated successfully.', $leaveType);
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::where('id', $id)
                              ->first();

        if (!$leaveType) {
             return $this->error('Leave type not found.');
        }

        $leaveType->delete();

        return $this->success(true, 'Leave type deleted successfully.');
    }



}
