<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('cash',15,2)->nullable();
            $table->decimal('bank',15,2)->nullable();
            $table->decimal('bkash',15,2)->nullable();
            $table->decimal('nagad',15,2)->nullable();
            $table->decimal('rocket',15,2)->nullable();
            $table->decimal('cheque',15,2)->nullable();
            $table->decimal('return_paid_amount',15,2)->nullable();
            $table->string('payment_status')->nullable();
            $table->string('document')->nullable();
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
        Schema::dropIfExists('sale_return_payments');
    }
}
