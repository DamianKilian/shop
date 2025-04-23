<?php

use App\Models\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        OrderStatus::create(['name' => 'Paid']);
        OrderStatus::create(['name' => 'Confirmed']);
        OrderStatus::create(['name' => 'Shipped']);
        OrderStatus::create(['name' => 'In Transit']);
        OrderStatus::create(['name' => 'In delivery']);
        OrderStatus::create(['name' => 'Delivered']);
        OrderStatus::create(['name' => 'Attempted Delivery']);
        OrderStatus::create(['name' => 'Awaiting Pickup']);
        OrderStatus::create(['name' => 'Delayed']);
        OrderStatus::create(['name' => 'Lost']);
        OrderStatus::create(['name' => 'Canceled']);

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('session_Id', length: 100);
            $table->decimal('price');
            $table->string('delivery_method');
            // $table->string('payment_type');
            $table->unsignedBigInteger('order_status_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('order_status_id')
                ->references('id')
                ->on('order_statuses');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')
                ->references('id')
                ->on('addresses');
            $table->unsignedBigInteger('address_invoice_id')->nullable();
            $table->foreign('address_invoice_id')
                ->references('id')
                ->on('addresses');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->unsigned();
            $table->unsignedBigInteger('product_id')->unsigned();
            $table->integer('num')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')
                ->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('orders');
    }
};
