<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'site_manager_id',
        'is_site_manager',
        'site_id',
        'shift_id',
        'check_in',
        'check_out',
        'lat',
        'long',
        'is_correction',
    ];



    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function siteManager()
    {
        return $this->belongsTo(Employee::class, 'site_manager_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }


   protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
