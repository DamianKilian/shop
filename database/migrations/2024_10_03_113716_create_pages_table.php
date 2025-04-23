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
            $table->text('body')->nullable();
            $table->text('body_prod')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Page::create([
            'title' => 'Home Page',
            'slug' => null,
            'body' => '{"time":1731146119711,"blocks":[{"id":"xmvRdvrELy","type":"paragraph","data":{"text":"Home page<br>"}}],"version":"2.30.6"}',
            'active' => true,
        ]);

        Page::create([
            'title' => 'Preview page',
            'slug' => config('my.preview_slug'),
            'body' => '{"time":1731146119711,"blocks":[{"id":"xmvRdvrELy","type":"paragraph","data":{"text":"Preview page<br>"}}],"version":"2.30.6"}',
            'active' => true,
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
