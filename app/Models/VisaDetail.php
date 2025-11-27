<?php

namespace App\Models;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'visa_status',
        'visa_issue_date',
        'visa_number',
        'visa_expiry_date',
        'share_code',
        'visa_document',
        'country',
        'country_code',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }



    public function getVisaDocumentAttribute($value)
    {
        if ($value) {
            return asset('images/employee/visa_document/' . $value);
        }
        return null;
    }


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
