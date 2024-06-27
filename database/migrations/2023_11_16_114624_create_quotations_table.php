<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('variation_id')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->string('discount_amount')->nullable();
            $table->decimal('sub_totals',15,2)->nullable();
            $table->string('vat_percentage')->nullable();
            $table->decimal('vat_amount',15,2)->nullable();
            $table->string('discount_type_subtotal')->nullable();
            $table->string('discount_on_subtotal')->nullable();
            $table->decimal('discount_on_subtotal_amount',15,2)->nullable();
            $table->decimal('shipping_amount',15,2)->nullable();
            $table->decimal('total_amount',15,2)->nullable();
            $table->string('status')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
