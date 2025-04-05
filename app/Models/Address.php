<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'surname',
        'nip',
        'company_name',
        'phone',
        'street',
        'house_number',
        'apartment_number',
        'postal_code',
        'area_code_id',
        'user_id',
        'city',
    ];
}
