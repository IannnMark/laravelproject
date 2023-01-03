<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderinfo', function (Blueprint $table) {
            $table->increments('orderinfo_id');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->date('date_placed');
            $table->timestamps();
        });

        Schema::create('orderline', function (Blueprint $table) {
               $table->integer('orderinfo_id')->unsigned();
               $table->foreign('orderinfo_id')->references('orderinfo_id')->on('orderinfo');
               $table->integer('product_id')->unsigned();
               $table->foreign('product_id')->references('id')->on('products');
               $table->integer('quantity');
        });
    }
  
    //orderline item id quantity
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
