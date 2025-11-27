<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'employer_id',
        'occasion',
        'start_date',
        'end_date',
    ];

    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


}
