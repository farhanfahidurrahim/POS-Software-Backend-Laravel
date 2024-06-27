<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('category_id')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('sub_sku')->nullable();
            $table->string('product_barcode')->nullable();
            $table->string('images')->nullable();
            $table->bigInteger('variation_template_id')->nullable();
            $table->bigInteger('variation_value_id')->nullable();
            $table->decimal('default_purchase_price', 22, 2)->nullable();
            $table->decimal('profit_percent', 22, 2)->default(0)->nullable();
            $table->decimal('default_sell_price', 22, 2)->nullable();
            $table->string('stock_amount')->nullable();
            $table->integer('alert_quantity')->nullable();
            $table->index('name');
            $table->index('sub_sku');
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
        Schema::dropIfExists('variations');
    }
}
