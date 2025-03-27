<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs_data', function (Blueprint $table) {
            $table->id();
            $table->string('data_key')->unique();
            $table->integer('error_num')->nullable();
            $table->timestamps();
        });
        $subYear = now()->subYear();

        DB::table('logs_data')->insert([
            'data_key' => 'log_js',
            'error_num' => 0,
            'updated_at' => $subYear,
        ]);

        DB::table('logs_data')->insert([
            'data_key' => 'log_http',
            'error_num' => 0,
            'updated_at' => $subYear,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_data');
    }
};
