<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->text("desc")->nullable();
            $table->string("input_type");
            $table->string("value");
            $table->string("default_value");
            $table->unsignedInteger('order_priority')->nullable();
            $table->unsignedBigInteger('setting_category_id');
            $table->foreign('setting_category_id')->references('id')->on('setting_categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
