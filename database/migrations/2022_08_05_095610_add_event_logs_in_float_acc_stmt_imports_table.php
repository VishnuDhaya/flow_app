<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventLogsInFloatAccStmtImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('float_acc_stmt_imports', function (Blueprint $table) {
            $table->json('event_logs')->after('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('float_acc_stmt_imports', function (Blueprint $table) {
            $table->dropColumn('event_logs');
        });
    }
}
