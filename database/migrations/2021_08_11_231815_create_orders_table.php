<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('product_quantity');
            $table->integer('product_price');
            $table->string('customer_email', 120);
            $table->string('customer_name', 80);
            $table->string('customer_mobile', 40);
            $table->integer('total');
            $table->enum('status', ['CREATED', 'PAYED', 'REJECTED','PENDING']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
