<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    protected $table = 'correction_requests';

    protected $fillable = [
        'employee_id',
        'site_manager_id',
        'site_id',
        'check_in',
        'name',
        'text',
        'type',
        'status',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function siteManager()
    {
        return $this->belongsTo(SiteManager::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
