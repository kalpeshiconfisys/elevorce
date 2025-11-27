<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $table = 'tasks';


    protected $fillable = [
        'employer_id',
        'employee_id',
        'site_id',
        'title',
        'description',
        'status',
        'assign_by',
        'start_date',
        'due_date',
        'end_date',
    ];


    // Timestamps are enabled by default
    public $timestamps = true;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
