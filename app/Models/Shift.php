<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';

    protected $fillable = [
        'name',
        'employer_id',
        'site_id',
        'start_time',
        'end_time',
        'status',
    ];

    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
