<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPhoto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['url', 'url_small', 'position', 'size', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
