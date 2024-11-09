<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->text('body');
            $table->timestamps();
        });

        Page::create([
            'title' => __('Home Page'),
            'slug' => null,
            'body' => '{"time":1731146119711,"blocks":[{"id":"xmvRdvrELy","type":"paragraph","data":{"text":"Home page<br>"}}],"version":"2.30.6"}',
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
