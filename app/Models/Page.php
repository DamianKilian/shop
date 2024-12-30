<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'body', 'body_prod', 'active'];

    public function pageFiles(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
