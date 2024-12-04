<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SettingValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'order_priority',
        'setting_id',
    ];

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class);
    }
}
