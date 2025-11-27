<?php

namespace App\Http\Controllers\admin\Shift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index($employer_id)
    {
        $shifts = Shift::where('employer_id', $employer_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($shift) {
                $shift['company_name'] = User::where('id', $shift->employer_id)->value('company_name');
                $shift['site_name'] = Site::where('id', $shift->site_id)->value('name');
                return $shift;
            });


        return view('admin.view.shift.index', compact('shifts','employer_id'));
    }

    public function create($employer_id)
    {
        $sites = Site::where('employer_id', $employer_id)->get();
        return view('admin.view.shift.create', compact('employer_id','sites'));
    }

    public function store(Request $request)
    {

        Shift::create([
            'employer_id' => $request->employer_id,
            'site_id'     => $request->site_id,
            'name'        => $request->name,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return redirect(url('admin/shift/list/'.$request->employer_id))->with('success', 'Shift created successfully.');
    }

    public function edit($id, $employer_id)
    {
        $sites = Site::where('employer_id', $employer_id)->get();
        $shift = Shift::find($id);
        if (!$shift) {
            return back()->with('error', 'Shift not found.');
        }
        return view('admin.view.shift.edit', compact('shift','employer_id','sites'));
    }

    public function show($id, $employer_id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return back()->with('error', 'Shift not found.');
        }
        return view('admin.view.shift.show', compact('shift','employer_id'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return back()->with('error', 'Shift not found.');
        }

        $shift->update([
            'name'       => $request->name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'site_id'    => $request->site_id,
        ]);

        return redirect(url('admin/shift/list/'.$request->employer_id))->with('success', 'Shift updated successfully.');
    }

    public function destroy($id, $employer_id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return back()->with('error', 'Shift not found.');
        }

        $shift->delete();

        return redirect(url('admin/shift/list/'.$employer_id))->with('success', 'Shift deleted successfully.');
    }
}
