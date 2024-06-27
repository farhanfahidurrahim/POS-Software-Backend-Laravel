<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->nullable();
            $table->string('barcode_path')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('variation_id')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->string('discount_amount')->nullable();
            $table->decimal('sub_totals', 15, 2)->nullable();
            $table->string('discount_type_subtotal')->nullable();
            $table->string('discount_on_subtotal')->nullable();
            $table->decimal('discount_on_subtotal_amount', 15, 2)->nullable();
            $table->decimal('shipping_charge', 15, 2)->nullable();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->string('delivery_method')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->decimal('due_amount', 15, 2)->nullable();
            $table->decimal('cash', 15, 2)->nullable();
            $table->string('cash_number')->nullable();
            $table->decimal('bank', 15, 2)->nullable();
            $table->string('bank_number')->nullable();
            $table->decimal('bkash', 15, 2)->nullable();
            $table->string('bkash_number')->nullable();
            $table->decimal('nagad', 15, 2)->nullable();
            $table->string('nagad_number')->nullable();
            $table->decimal('rocket', 15, 2)->nullable();
            $table->string('rocket_number')->nullable();
            $table->decimal('cheque', 15, 2)->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('note')->nullable();
            $table->text('sale_from')->nullable();
            $table->boolean('dispatch_status')->default(0)->nullable()->comment('0 => No, 1 => Yes');;
            $table->string('dispatch_date')->default(0)->nullable();
            $table->string('status')->nullable();
            $table->string('sale_return')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
