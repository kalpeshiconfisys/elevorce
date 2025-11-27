<?php



namespace App\Http\Controllers\admin\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\Site;

class EmployeeController extends Controller
{
    public function index($employer_id = 0, Request $request)
    {


        $employer_id = base64_decode($employer_id);



        $redirectType =  base64_decode($request->query('type')) ?? 1;
        if ($employer_id == 0) {
            $hideSite = base64_decode($request->query('hide_site'));

            $sites = Site::where('id', $hideSite)->first();
            $totalSites = Site::where('employer_id', $sites->employer_id)->pluck('id')->toArray();
            $employees = Employee::with('visa_details')->whereIn('site_id', $totalSites)->paginate(10);
            $allSites = Site::where('employer_id', $sites->employer_id)->get();
            $shifts = Shift::where('site_id', $sites->id)->get();
            if (!$sites) {
                return redirect()->back()->with('error', 'Site not found');
            }
        } else {
            $sites = Site::where('id', $employer_id)->first();

            $shifts = Shift::where('site_id', $employer_id)->get();
            if (!$sites) {
                return redirect()->back()->with('error', 'Site not found');
            }
            $allSites = Site::where('employer_id', $sites->employer_id)->get();
            $employees = Employee::with('visa_details')
                ->where('site_id', $employer_id)
                ->paginate(10);
        }

        return view('admin.view.employee.index', compact('employees', 'employer_id', 'allSites', 'redirectType', 'shifts'));
    }


    public function updateShift(Request $request, $id)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $employee = Employee::find($id);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        $employee->shift_id = $request->shift_id;
        $employee->save();

        return redirect()->back()->with('success', 'Shift updated successfully');
    }
}
