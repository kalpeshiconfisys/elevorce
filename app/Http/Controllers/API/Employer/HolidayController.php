<?php

namespace App\Http\Controllers\API\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Site;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $holidays = Holiday::where('employer_id', auth()->user()->id)->whereYear('created_at', $request->year)->orderBy('created_at', 'desc');
        $holidays = $holidays->get();

        return $this->success(true, 'Holidays retrieved successfully.', $holidays);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'occasion'   => 'required',
            'start_date' => 'required|date_format:d-m-Y',
            'end_date'   => 'required|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employer_id' => auth()->user()->id,
            'occasion'   => $request->occasion,
            'start_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
            'end_date'   => \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d')
        ];
        $holiday = Holiday::create($input);
        return $this->success(true, 'Holiday created successfully.', $holiday);
    }

    public function show($id)
    {
        $holiday = Holiday::where('id', $id)
            ->where('employer_id', auth()->user()->id)
            ->first();

        if (!$holiday) {
            return $this->success(false, 'Holiday not found.', 404);
        }

        return $this->success(true, 'Holiday retrieved successfully.', $holiday);
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::where('id', $id)->first();

        if (!$holiday) {
            return $this->error('Holiday not found.');
        }

        $validator = Validator::make($request->all(), [
            'occasion'   => 'required',
            'start_date' => 'required|date_format:d-m-Y',
            'end_date'   => 'required|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $input = [
            'employer_id' => auth()->user()->id,
            'occasion'   => $request->occasion,
            'start_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
            'end_date'   => \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d')
        ];
        Holiday::where('id', $id)->update($input);
        return $this->success(true, 'Holiday updated successfully.', []);
    }

    public function destroy($id)
    {
        $holiday = Holiday::where('id', $id)->first();
        if (!$holiday) {
            return $this->success(false, 'Holiday not found.', 404);
        }
        $holiday->delete();
        return $this->success(true, 'Holiday deleted successfully.');
    }

    public function getHolidayList(Request $request)
    {

        $limit = $request->limit ?? 9;
        $offset = $request->offset ?? 0;
        $site = Site::find(auth()->user()->site_id);
        $holidays = Holiday::where('employer_id', $site->employer_id);

        $holidays = $holidays->skip($offset * $limit)->take($limit)->get();
        return $this->success(true, 'Holiday Get successfully.', $holidays);
    }
}
