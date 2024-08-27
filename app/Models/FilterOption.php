<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FilterOption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'order_priority', 'filter_id'];

    public function flight()
    {
        return $this->belongsTo(Filter::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
