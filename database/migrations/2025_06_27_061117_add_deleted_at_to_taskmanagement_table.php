<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToTaskmanagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taskmanagement', function (Blueprint $table) {
            $table->softDeletes(); // This adds a nullable deleted_at TIMESTAMP column
        });
    }

    public function down()
    {
        Schema::table('taskmanagement', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
