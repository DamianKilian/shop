<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order_priority',
    ];

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
