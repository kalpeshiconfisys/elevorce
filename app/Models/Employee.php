<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table = 'employees';


    protected $fillable = [
        'employer_id',
        'site_id',
        'shift_id',
        'site_manager_id',
        'embedding',
        'name',
        'email',
        'address',
        'is_promotion',
        'number',
        'password',
        'image',
        'selfie',
        'status',
        'created_by',
        'is_login',
        'device_type',
        'device_token',
        'app_version',
        'mobile_version',
    ];


    protected $hidden = [
        'password',
        'embedding'
    ];


    protected $casts = [
        'embedding' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope('activeStatus', function (Builder $builder) {
            $builder->where('status', 1);
        });
    }

    public function getImageAttribute($value)
    {
        if ($value != NULL) {
            return asset('images/employee/' . $value);
        }
        return null;
    }

    public function getSelfieAttribute($value)
    {
        if ($value != NULL) {
            return asset('images/employee/selfie/' . $value);
        }
        return null;
    }


    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }


    public function employer()
    {
        return $this->belongsTo(User::class);
    }


    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }


    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function siteManager()
    {
        return $this->belongsTo(SiteManager::class, 'site_manager_id');
    }

    public function visa_details()
    {
        return $this->hasOne(VisaDetail::class, 'employee_id');
    }



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
