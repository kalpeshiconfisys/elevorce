<?php



namespace App\Http\Controllers\admin\LeaveType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LeaveTypeController extends Controller
{

    public function index($employer_id)
    {

        

        $employer_id = base64_decode($employer_id);



        $leaveTypes = LeaveType::where('employer_id', $employer_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes->getCollection()->transform(function ($leaveType) {
            $leaveType['company_name'] = User::where('id', $leaveType->employer_id)->value('company_name');
            return $leaveType;
        });



        return view('admin.view.leave-type.index', compact('leaveTypes', 'employer_id'));
    }

    public function create($employer_id)
    {
        $employer_id = base64_decode($employer_id);
        return view('admin.view.leave-type.create', compact('employer_id'));
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
            'employer_id' => $request->employer_id,
            'leave_name'  => $request->leave_name,
            'count'       => $request->count,
        ];


        $leaveType = LeaveType::create($input);


     $encodedEmployerId = trim(base64_encode($request->employer_id), '=');

        return redirect(url('admin/leave-type/list/' . $encodedEmployerId))
       ->with('success', 'Leave type created successfully.');
    }

    public function edit($id, $employer_id)
    {
        $employer_id = base64_decode($employer_id);
        $id = base64_decode($id);
        $leaveType = LeaveType::where('id', $id)->first();
        if (!$leaveType) {
            return $this->error('Leave type not found.');
        }
        return view('admin.view.leave-type.edit', compact('leaveType', 'employer_id'));
    }

    public function show($id, $employer_id)
    {
        $employer_id = base64_decode($employer_id);
        $id = base64_decode($id);
        $leaveType = LeaveType::where('id', $id)->first();
        if (!$leaveType) {
            return $this->error('Leave type not found.');
        }
        return view('admin.view.leave-type.show', compact('leaveType', 'employer_id'));
    }

    public function update(Request $request, $id)
    {
        $id = base64_decode($id);
        $leaveType = LeaveType::where('id', $id)->first();



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

         $encodedEmployerId = trim(base64_encode($request->employer_id), '=');
        return redirect(url('admin/leave-type/list/' . $encodedEmployerId))->with('success', 'Leave type updated successfully.');
    }

    public function destroy($id, $employer_id)
    {
        $employer_id = base64_decode($employer_id);
        $id = base64_decode($id);
        $leaveType = LeaveType::where('id', $id)
            ->first();

        if (!$leaveType) {
            return $this->error('Leave type not found.');
        }

        $leaveType->delete();

                $encodedEmployerId = trim(base64_encode($employer_id), '=');
        return redirect(url('admin/leave-type/list/' . $encodedEmployerId))->with('success', 'Leave type deleted successfully.');
    }
}
