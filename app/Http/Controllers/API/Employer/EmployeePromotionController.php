<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Site;
use App\Models\SiteManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeePromotionController extends Controller
{
    public function getEmployeeForPromotion(Request $request)
    {
        $employer = auth()->user();
        $limit = $request->limit;
        $offset = $request->offset;
        $sites = Site::where('employer_id', $employer->id)->pluck('id')->toArray();
        $employees = Employee::whereIn('site_id', $sites)->where('is_promotion',0)->orderBy('created_at','desc');
        if(!empty($request->limit)){
            $employees = $employees->skip($offset * $limit)->take($limit);
        }
        $employees = $employees->get();
        
        return $this->success(true, 'Employees retrieved successfully', $employees);
    }

    public function employeePromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'     => 'nullable|string|max:255',
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable|email|unique:site_managers,email',
            'phone' => 'nullable|string|max:15|unique:site_managers,phone',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $employee = Employee::find($request->employee_id);
        $employee->is_promotion = 1;
        $employee->save();
        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        $input['employer_id'] = auth()->user()->id;

        if ($request->hasFile('image')) {
            $imageName = time() . rand(1000, 9999) . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images/site_manager'), $imageName);
            $input['image'] = $imageName;
        }

        SiteManager::create($input);

        return $this->success(true, 'Employee Promotion Successfully.', []);
    }
}
