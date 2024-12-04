<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_setting_value', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_id')->unsigned();
            $table->unsignedBigInteger('setting_value_id')->unsigned();
            $table->foreign('setting_id')->references('id')->on('settings')
                ->onDelete('cascade');
            $table->foreign('setting_value_id')->references('id')->on('setting_values')
                ->onDelete('cascade');
        });
    }

    public function down(): void {}
};
