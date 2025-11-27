<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'shift_id',
        'leave_sub_type_id',
        'leave_reason',
        'status',
        'leave_status',
        'from_date',
        'to_date',
        'time_range',
        'half_day_type',
        'rejected_reason',
        'report_sickness',
        'is_emergency'
    ];

    public $timestamps = true;

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function leaveSubType()
    {
        return $this->belongsTo(LeaveSubType::class, 'leave_sub_type_id');
    }

     public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }



    public function getReportSicknessAttribute($value)
    {
        if ($value) {
            return asset('images/leave/report_sickness/' . $value);
        }
        return null;
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


}
