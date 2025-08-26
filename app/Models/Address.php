<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'country_id',
    ];

    public static function createRules($prefix = '', $required = 'required')
    {
        return [
            $prefix . 'email' => [$required, 'email'],
            $prefix . 'name' => [$required],
            $prefix . 'surname' => [$required],
            $prefix . 'phone' => [$required, 'size:9'],
            $prefix . 'street' => [$required],
            $prefix . 'house_number' => [$required],
            $prefix . 'postal_code' => [$required, 'regex:/^\d{2}-?\d{3}$/'],
            $prefix . 'area_code_id' => [$required],
            $prefix . 'city' => [$required],
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function areaCode(): BelongsTo
    {
        return $this->belongsTo(AreaCode::class);
    }
}
