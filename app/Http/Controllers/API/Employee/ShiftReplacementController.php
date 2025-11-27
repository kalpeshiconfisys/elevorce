<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReplacementShift;
use Illuminate\Support\Facades\Auth;

class ShiftReplacementController extends Controller
{
    public function employeeShiftReplacementList(Request $request)
    {
        $employee = Auth::user();   
        $limit = $request->limit;
        $offset = $request->offset;
        $replacements = ReplacementShift::with(['leave.employee.shift', 'assign_employee'])->where('employee_id', $employee->id)->orderBy('id', 'desc');
        if(!empty($request->limit)){
            $replacements = $replacements->skip($offset * $limit)->take($limit);
        }
        $replacements = $replacements->get();
        return $this->success(true, 'Shift replacement list retrieved successfully.', $replacements);
    }
}
