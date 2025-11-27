<?php

namespace App\Http\Controllers\API\SiteManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReplacementShift;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ShiftReplacementController extends Controller
{

    public function shiftReplace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_id'      => 'required|exists:leaves,id',
            'employee_id'  => 'required|exists:employees,id',
            'note'         => 'nullable|string',
        ]);


        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $replacement = ReplacementShift::create([
            'employee_id'  => $request->employee_id,
            'note'         => $request->note ?? '',
            'leave_id'     => $request->leave_id ,
        ]);

        return $this->success(true, 'Shift replacement recorded successfully.', $replacement);
    }


    public function shiftReplacementList(Request $request)
    {
        $siteManager = auth()->user();

        $limit = $request->limit;
        $offset = $request->offset;

        $employees = Employee::where('site_id', $siteManager->site_id)->pluck('id')->toArray();

        $replacements = ReplacementShift::with(['leave.employee.shift', 'assign_employee'])->wherein('employee_id', $employees)->orderBy('created_at', 'desc');

        if(!empty($request->limit)){
            $replacements = $replacements->skip($offset * $limit)->take($limit);
        }
        $replacements = $replacements->get();

        return $this->success(true, 'Shift replacement list retrieved successfully.', $replacements);
    }

    public function getEmployeeWithoutLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'  => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $limit = $request->limit;
        $offset = $request->offset;
        $absentEmployee = Employee::find($request->employee_id);

        // Aaje ni date
        $today = now()->toDateString();

        // Employees fetch karo:
        // 1) Jeni shift alag hoy
        // 2) Aaje leave table ma approved leave na hoy
        $employees = Employee::with(['visa_details'])->where('site_id',$absentEmployee->site_id)->where('shift_id', '!=', $absentEmployee->shift_id)
            ->whereDoesntHave('leaves', function ($query) use ($today) {
                $query->where('status', 'approved')
                    ->whereDate('from_date', '<=', $today)
                    ->whereDate('to_date', '>=', $today);
            })
            ;
            if(!empty($request->limit)){
                $employees = $employees->skip($offset * $limit)->take($limit);
            }
            $employees = $employees->get();

        return $this->success(true, 'list retrieved successfully.', $employees);
    }



}

