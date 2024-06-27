<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_transaction_payments', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->bigInteger('supplier_transaction_id')->nullable();
            $table->bigInteger('supplier_id')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('due_amount')->nullable();
            $table->string('note')->nullable();
            $table->string('document')->nullable();
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
        Schema::dropIfExists('supplier_transaction_payments');
    }
}