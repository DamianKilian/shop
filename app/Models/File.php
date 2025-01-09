<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'data', 'page_id', 'product_id', 'position', 'url_thumbnail', 'display_type'];

    public $data;

    public function data()
    {
        if ($this->data) {
            return $this->data;
        } else {
            return $this->data = json_decode($this->attributes['data']);
        }
    }
}
