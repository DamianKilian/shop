<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SelfReferenceTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SelfReferenceTrait;
    use SoftDeletes;

    protected $fillable = ['parent_id', 'name', 'position', 'deleted_at',];

    public function products()
    {
        return $this->hasMany('App\Product');
    }
}
