<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;


class SiteManager extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table = 'site_managers'; // Optional if naming convention is followed

    protected $fillable = [
        'employer_id',
        'site_id',
        'name',
        'email',
        'number',
        'password',
        'image',
        'selfie',
        'address',
        'status',
        'country',
        'country_code',
        'is_login',
        'device_type',
        'device_token',
        'app_version',
        'mobile_version',
        'embedding',
    ];

    protected $hidden = [
        'password',
         'embedding'
    ];


    protected $casts = [
        'embedding' => 'array',
    ];


    protected $dates = [
        'deleted_at',
    ];

    public $timestamps = true;


    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }



    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('images/site_manager/' . $value);
        }
        return null;
    }

    public function getSelfieAttribute($value)
    {
        if ($value) {
            return asset('images/site_manager/selfie/' . $value);
        }
        return null;
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
