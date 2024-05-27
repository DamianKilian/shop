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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('description');
            $table->decimal('price');
            $table->integer('quantity');
            $table->string('gtin')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('product_photos', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('url_small');
            $table->integer('position');
            $table->unsignedBigInteger('size');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_photos');
    }
};
