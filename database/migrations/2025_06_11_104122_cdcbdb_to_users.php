<?php
// crated by, deleted by , deleted at
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CdcbdbToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('created_by')->unsigned()->nullable()->after('role_id');
            $table->integer('deleted_by')->unsigned()->nullable()->after('created_by');
            $table->softDeletes('deleted_at')->nullable()->after('deleted_by');

            // Indexing
            //$table->index(['created_by', 'deleted_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('deleted_by');
            $table->dropColumn('deleted_at');   
        });
    }
}
