<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisbStmtIntTypeColumnsInAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('disb_int_type',5)->nullable()->after("int_type");
            $table->string('stmt_int_type',5)->nullable()->after("disb_int_type");
        });

        DB::update("update accounts set disb_int_type = 'web', stmt_int_type = 'web' where id in (1783, 2895)");
        DB::update("update accounts set disb_int_type = 'mob', stmt_int_type = 'web' where id = 4161");
        DB::update("update accounts set stmt_int_type = 'web' where id in (3605, 4094, 4183, 4184, 4185)");

        DB::statement("alter table accounts drop column int_type");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('disb_int_type');
            $table->dropColumn('stmt_int_type');
        });
    }
}
