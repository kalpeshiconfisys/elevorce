<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $fillable = [
        'employer_id',
        'leave_name',
        'status',
        'count',
        'time_range',
    ];

    public $timestamps = true;
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
