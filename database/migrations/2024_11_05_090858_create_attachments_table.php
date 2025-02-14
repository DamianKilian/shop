<?php

use App\Services\AppService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->foreign('page_id')
                ->references('id')
                ->on('pages');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')
                ->references('id')
                ->on('products');
            $table->timestamps();
        });
        if (!AppService::isSqlite($this)) {
            DB::statement('ALTER TABLE attachments ADD CONSTRAINT chk_page_id_product_id CHECK ( 1 = GREATEST(ISNULL(page_id), ISNULL(product_id)));');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
