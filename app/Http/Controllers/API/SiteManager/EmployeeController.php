<?php

namespace App\Http\Controllers\API\SiteManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateEmbeddingsJob;
use Illuminate\Support\Facades\File;
use App\Models\Employee;
use App\Models\SiteManager;
use App\Models\VisaDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{

    public function index(Request $request, $id)
    {
        $limit = $request->limit;
        $offset = $request->offset;

        $employeesData = Employee::with(['visa_details'])->where('site_id', $id)->where('is_promotion', 0)->orderBy('created_at', 'desc');

        if(!empty($request->limit)){
           $employees = $employeesData->skip($offset * $limit)->take($limit);
        }
        $employees = $employeesData->get();

        return $this->success(true, 'Employees retrieved successfully', $employees);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:site_manager,employer',
            'name'           => 'required',
            'site_id'          => 'required',
            'email'         => 'required|email|unique:employees,email',
            'number'         => 'required|unique:employees,number',
            'address'         => 'required',
            'password'          => 'required',
            'visa_issue_date'       => 'required',
            'visa_number'       => 'required',
            'visa_expiry_date'       => 'required',
            'share_code'       => 'required',
            'country'       => 'required',
            'image'         => 'required|mimes:jpg,jpeg,png,gif|max:2048',
            'visa_document'  => 'required|mimes:jpg,jpeg,png,,gif,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . rand(1000, 9999) . '.' . $extension;
            $selfieName = time() . rand(1000, 9999) . '.' . $extension;
            $file->move(public_path('images/employee'), $imageName);
            File::copy(public_path('images/employee/' . $imageName), public_path('images/employee/selfie/' . $selfieName));
        }

        $data = [
            'site_id'    => $request->site_id,
            'shift_id'   => $request->shift_id,
            'name'       => $request->name,
            'email'      => $request->email,
            'image'      => $imageName ?? null,
            'selfie'     => $selfieName ?? null,
            'number'     => $request->number,
            'password'   => Hash::make($request->password),
            'created_by' => $request->type,
            'address'    => $request->address,
        ];

        if ($request->type == 'site_manager') {
            $data['site_manager_id'] = auth()->user()->id;
        } elseif ($request->type == 'employer') {
            $data['employer_id'] = auth()->user()->id;
        }

        $employee = Employee::create($data);
        // GenerateEmbeddingsJob::dispatch();

        $visaDocName = null;
        if ($request->hasFile('visa_document')) {
            $visaDocName = time() . rand(1000, 9999) . '.' . $request->file('visa_document')->extension();
            $request->file('visa_document')->move(public_path('images/employee/visa_document'), $visaDocName);
        }

        $visaStatus = Carbon::parse($request->visa_expiry_date)->isFuture() ? 'active' : 'expired';
        VisaDetail::create([
            'employee_id'      => $employee->id,
            'visa_status'      => $visaStatus,
            'visa_issue_date'  => \Carbon\Carbon::createFromFormat('d-m-Y', $request->visa_issue_date)->format('Y-m-d'),
            'visa_number'      => $request->visa_number,
            'visa_expiry_date' => \Carbon\Carbon::createFromFormat('d-m-Y',  $request->visa_expiry_date)->format('Y-m-d'),
            'share_code'       => $request->share_code,
            'visa_document'    => $visaDocName,
            'country'          => $request->country,
            'country_code'     => $request->country_code,
        ]);

        $employeeData = Employee::with('visa_details')->find($employee->id);
        return $this->success(true, 'Employee  created successfully.', $employeeData);
    }


    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return $this->error('Employee not found.');
        }

        $employee->status = 0;
        $employee->save();
        $employee->delete();
        return $this->success(true, 'Employee deleted successfully.');
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::withTrashed()->find($id);

        if (!$employee) {
            return $this->error('Employee not found.');
        }
        $employee->forceDelete();
        return $this->success(true, 'Employee deleted successfully.');
    }




    // public function pyEmployeeList($id)
    // {
    //     $query = Employee::select('id', 'selfie')->where('site_id', $id)->whereNull('embedding')->where('status', 1)->orderBy('id', 'DESC')->get();

    //     return $this->success(true, 'Employee get successfully.', $query);
    // }

    // public function pyAllEmployeeList($id)
    // {
    //     $query = Employee::select('id', 'selfie', 'embedding')->where('site_id', $id)->where('status', 1)->orderBy('id', 'DESC')->get();
    //     $query->makeVisible('embedding');
    //     return $this->success(true, 'Employee get successfully.', $query);
    // }


    public function pyEmployeeList($id)
    {

        $employees = Employee::select('id', 'selfie')
            ->where('site_id', $id)
            ->whereNull('embedding')
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $employees->makeVisible('embedding');

        $employees->transform(function ($item) {
            $item->is_sitemanager = false;
            return $item;
        });

        $siteManagers = SiteManager::select('id', 'selfie')
            ->where('site_id', $id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $siteManagers->makeVisible('embedding');
        $siteManagers->transform(function ($item) {

            $item->is_sitemanager = true;
            return $item;
        });

        $finalList = $employees->concat($siteManagers);

        return $this->success(true, 'Employee & Site Manager fetched successfully.', $finalList);
    }

    public function pyAllEmployeeList($id)
    {
        $employees = Employee::select('id', 'selfie', 'embedding')
            ->where('site_id', $id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $employees->makeVisible('embedding');

        $employees->transform(function ($item) {

            $item->is_sitemanager = false;
            return $item;
        });

        $siteManagers = SiteManager::select('id', 'selfie', 'embedding')
            ->where('site_id', $id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $siteManagers->makeVisible('embedding');

        $siteManagers->transform(function ($item) {

            $item->is_sitemanager = true;
            return $item;
        });

        $finalList = $employees->concat($siteManagers);


        return $this->success(true, 'Employee & Site Manager fetched successfully.', $finalList);
    }

    public function pyUpdateEmbeddingEmployee(Request $request)
    {

        if ($request->is_sitemanager == true) {
            $siteManager = SiteManager::find($request->employee_id);
            if (!$siteManager) {
                return $this->error('Site Manager not found.');
            }
            if (is_string($request->embedding)) {
                $decoded = json_decode($request->embedding, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $siteManager->embedding = $decoded;
                } else {
                    return $this->error('Invalid embedding format.');
                }
            } else {
                $siteManager->embedding = $request->embedding;
            }
            $siteManager->save();
        } else {

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return $this->error('Employee not found.');
            }

            if (is_string($request->embedding)) {
                $decoded = json_decode($request->embedding, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $employee->embedding = $decoded;
                } else {
                    return $this->error('Invalid embedding format.');
                }
            } else {
                $employee->embedding = $request->embedding;
            }
            $employee->save();
        }

        return $this->success(true, 'Embedding updated successfully.');
    }
}
