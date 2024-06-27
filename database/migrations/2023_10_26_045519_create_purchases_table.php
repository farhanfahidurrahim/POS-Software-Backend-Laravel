<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_date')->nullable();
            $table->string('invoice')->nullable();
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->string('variation_id')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('sub_total')->nullable();
            $table->decimal('sub_total_amount',15,2)->nullable();
            $table->decimal('shipping_amount',15,2)->nullable();
            $table->decimal('total_amount',15,2)->nullable();
            $table->decimal('paid_amount',15,2)->nullable();
            $table->decimal('due_amount',15,2)->nullable();
            $table->string('purchase_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_method')->nullable();
            $table->longText('note')->nullable();
            $table->string('return_purchase')->nullable();
            $table->string('document')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('purchases');
    }
}
