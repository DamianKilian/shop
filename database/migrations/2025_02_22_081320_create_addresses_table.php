<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name');
            $table->string('surname');
            $table->string('nip')->nullable();
            $table->string('company_name');
            $table->string('phone');
            $table->string('street');
            $table->string('house_number');
            $table->string('apartment_number')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->unsignedBigInteger('area_code_id');
            $table->foreign('area_code_id')
                ->references('id')
                ->on('area_codes');
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('default_address_id')->nullable();
            $table->unsignedBigInteger('default_address_invoice_id')->nullable();
            $table->foreign('default_address_id')
                ->references('id')
                ->on('addresses');
            $table->foreign('default_address_invoice_id')
                ->references('id')
                ->on('addresses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
