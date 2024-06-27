<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->nullable();
            $table->string('variation_id')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('reference')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->string('variation_qty')->nullable();
            $table->float('total_amount')->nullable();
            $table->string('document')->nullable();
            $table->longText('note')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('transfer_stocks');
    }
}
