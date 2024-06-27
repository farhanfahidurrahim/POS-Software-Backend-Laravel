<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('image')->nullable();
            $table->string('email')->unique();
            $table->string('user_name')->unique()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('alt_number')->nullable();
            $table->string('family_number')->nullable();
            $table->string('nid')->unique()->nullable();
            $table->string('passport')->unique()->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('current_address')->nullable();
            $table->string('bank_details')->nullable();
            $table->string('password');
            $table->string('user_type')->nullable()->default(1)->comment('1 for admin, 2 for staff');
            $table->tinyInteger('status')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
