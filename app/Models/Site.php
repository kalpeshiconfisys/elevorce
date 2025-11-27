<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{

    protected $table = 'sites';


    protected $fillable = [
        'employer_id',
        'name',
        'address',
        'assign_site_manager',
        'status',
        'phone',
        'email',
        'image',
    ];


    public $timestamps = true;


    public function siteManager()
    {
        return $this->hasOne(SiteManager::class, 'site_id');
    }

    public function employer()
    {
        return $this->belongsTo(User::class);
    }


    public function employee()
    {
        return $this->hasOne(Employee::class,'site_id');
    }

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('images/sites/' . $value);
        }
        return null;
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
