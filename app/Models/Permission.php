<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name',];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
