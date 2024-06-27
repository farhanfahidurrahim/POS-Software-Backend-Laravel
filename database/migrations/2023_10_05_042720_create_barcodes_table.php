<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('paper_width')->nullable();
            $table->string('paper_height')->nullable();
            $table->string('top_margin')->nullable();
            $table->string('left_margin')->nullable();
            $table->string('row_distance')->nullable();
            $table->string('col_distance')->nullable();
            $table->string('stickers_in_one_row')->nullable();
            $table->string('is_default')->nullable();
            $table->string('is_continuous')->nullable();
            $table->string('stickers_in_one_sheet')->nullable();
            $table->bigInteger('branch_id');
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
        Schema::dropIfExists('barcodes');
    }
}
