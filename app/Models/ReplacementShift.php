<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReplacementShift extends Model
{
    protected $table = 'replacement_shifts';

    protected $fillable = [
        'leave_id',
        'employee_id',
        'note',
        'status',
    ];

    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assign_employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }


    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
