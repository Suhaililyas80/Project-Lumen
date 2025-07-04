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
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
       // $table->integer('role_id')->unsigned()->nullable();
        $table->timestamps();

       // $table->bigIncrements('role_id')->unsigned();
        //$table->foreign('role_id')->references('id')->on('idrole')->onDelete('cascade');

        //soft delete column
        //creted by
        // deleted by
        // role id
        //indexing
        //
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
