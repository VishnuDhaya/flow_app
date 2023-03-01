<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAccountStmtsTableRecon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('account_stmts', function (Blueprint $table) {
            $table->string('recon_status',35)->nullable()->after('recon_id');
        });

        DB::update("ALTER TABLE account_stmts DROP COLUMN accounted");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
