<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'leave_id',
        'is_read',
        'employer_id',
        'employee_id',
        'site_manager_id',
        'type',
        'message',
        'description',
    ];

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
