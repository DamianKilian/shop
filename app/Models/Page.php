<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'body'];

    public function pageFiles(): HasMany
    {
        return $this->hasMany(PageFile::class);
    }
}
