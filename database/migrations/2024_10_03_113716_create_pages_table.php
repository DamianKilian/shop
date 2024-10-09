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
            $table->timestamps();
        });

        Page::create([
            'title' => __('Home Page'),
            'slug' => null,
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
