<?php

use App\Models\Product;
use App\Services\AppService;
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
            $table->text('description')->nullable();
            $table->text('description_str')->nullable();
            $table->text('description_prod')->nullable();
            $table->boolean('active')->default(false);
            $table->decimal('price');
            $table->integer('quantity');
            $table->string('gtin')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
            if (!AppService::isSqlite($this)) {
                $table->fullText(['title', 'description_str']);
            }
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_photos');
    }
};
