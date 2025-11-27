<?php

namespace App\Models;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployerAnnouncement extends Model
{
    protected $table = 'employer_announcements';

    protected $fillable = [
        'employer_id',
        'site_id',
        'employee_id',
        'message',
        'description',
    ];

    public $timestamps = true;



    public function employer()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
