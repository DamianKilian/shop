<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'page_id'];
}
