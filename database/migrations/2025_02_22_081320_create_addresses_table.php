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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('surname');
            $table->string('nip');
            $table->string('company_name');
            $table->string('phone');
            $table->string('street');
            $table->string('house_number');
            $table->string('apartment_number');
            $table->string('postal_code');
            $table->string('city');
            $table->unsignedBigInteger('area_code_id');
            $table->foreign('area_code_id')
                ->references('id')
                ->on('area_codes');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
