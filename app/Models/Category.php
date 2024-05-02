<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SelfReferenceTrait;

class Category extends Model
{
    use HasFactory;
    use SelfReferenceTrait;

    protected $fillable = ['parent_id', 'name', 'position',];
}
