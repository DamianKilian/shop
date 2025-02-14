<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'input_type',
        'value',
        'default_value',
        'order_priority',
        'setting_category_id',
    ];

    public function settingCategory()
    {
        return $this->belongsTo(SettingCategory::class);
    }

    public function settingValues(): BelongsToMany
    {
        return $this->belongsToMany(SettingValue::class);
    }
}
