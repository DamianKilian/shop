<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'price', 'quantity', 'category_id',];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function productPhotos()
    {
        return $this->hasMany('App\productPhoto');
    }
}
