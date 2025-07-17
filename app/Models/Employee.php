<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yajra\Address\HasAddress;

class Employee extends Model
{
    use SoftDeletes, HasAddress;

    protected $fillable = [
        'employee_number',
        'user_id',
        'department_id',
        'division_id',
        'position_id',
        'manager_id',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'professional_title',
        'email',
        'phone',
        'street',
        'region_id',
        'province_id',
        'city_id',
        'barangay_id',
        'zipcode',
        'birth_date',
        'hire_date',
        'termination_date',
        'gender',
        'marital_status',
        'salary',
        'employment_type',
        'job_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'tax_id',
        'social_security_no',
        'philhealth_no',
        'pagibig_no',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }


    // Name Formatters
    public function getFullNameAttribute()
    {
        $name = [$this->first_name];
        
        if ($this->middle_name) {
            $name[] = $this->middle_name;
        }
        
        $name[] = $this->last_name;
        
        if ($this->suffix) {
            $name[] = $this->suffix;
        }
        
        return implode(' ', $name);
    }

    public function getFormalNameAttribute()
    {
        $name = [];
        
        if ($this->prefix) {
            $name[] = $this->prefix;
        }
        
        $name[] = $this->first_name;
        
        if ($this->middle_name) {
            $name[] = substr($this->middle_name, 0, 1) . '.';
        }
        
        $name[] = $this->last_name;
        
        if ($this->professional_title) {
            $name[] = $this->professional_title;
        }
        
        return implode(' ', $name);
    }

    // Address Formatter
    public function getFullAddressAttribute()
    {
        $address = [
            $this->street,
            $this->barangay->name ?? null,
            $this->city->name ?? null,
            $this->province->name ?? null,
            $this->region->name ?? null,
            $this->zipcode
        ];

        return implode(', ', array_filter($address));
    }
}