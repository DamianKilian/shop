<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'order_priority',
        'setting_id',
    ];

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
