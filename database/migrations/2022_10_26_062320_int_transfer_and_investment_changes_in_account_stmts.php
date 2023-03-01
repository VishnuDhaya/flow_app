<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntTransferAndInvestmentChangesInAccountStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::update("update account_stmts set recon_status = '75_internal_transfer' where descr = 'Momo Outward transfer / 250791519171 MTN Payment' and country_code = 'RWA' and account_id = 4182");
        DB::update("update account_stmts set recon_status = '75_internal_transfer' where descr = 'Momo Outward transfer / 250791516469 MTN Payment' and country_code = 'RWA' and account_id = 4182");
        DB::update("update account_stmts set recon_status = '75_internal_transfer' where descr = 'Momo Outward transfer / 250791334419 MTN Payment' and country_code = 'RWA' and account_id = 4182");

        DB::update("update account_stmts set recon_status = '60_non_fa_credit' where descr = 'fund-transfer to 250791519171/BANK OF KIGALI PLC' and country_code = 'RWA' and account_id = 4183");
        DB::update("update account_stmts set recon_status = '60_non_fa_credit' where descr = 'fund-transfer to 250791516469/BANK OF KIGALI PLC' and country_code = 'RWA' and account_id = 4184");
        DB::update("update account_stmts set recon_status = '60_non_fa_credit' where descr = 'fund-transfer to 250791334419/BANK OF KIGALI PLC' and country_code = 'RWA' and account_id = 4185");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            //
        });
    }
}
