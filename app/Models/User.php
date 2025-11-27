<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'number',
        'image',
        'company_name',
        'company_address',
        'company_logo',
        'company_cover_image',
        'status',
        'country_code',
        'country',
        'device_type',
        'device_token',
        'app_version',
        'mobile_version',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    public function siteManager()
    {
        return $this->hasOne(SiteManager::class, 'employer_id');
    }


    public function site()
    {
        return $this->hasOne(Site::class, 'employer_id');
    }




    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('images/employer/' . $value);
        }
        return null;
    }


    public function getCompanyLogoAttribute($value)
    {
        if ($value) {
            return asset('images/employer/' . $value);
        }
        return null;
    }


    public function getCompanyCoverImageAttribute($value)
    {
        if ($value) {
            return asset('images/employer/' . $value);
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

    public function getProfileAttribute($value) {}


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
