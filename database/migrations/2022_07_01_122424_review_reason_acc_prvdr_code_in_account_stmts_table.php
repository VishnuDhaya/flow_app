<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReviewReasonAccPrvdrCodeInAccountStmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            $table->string('review_reason',20)->nullable()->after("recon_desc");
            $table->string('acc_prvdr_code',6)->nullable()->after("account_id");
            $table->string('lender_acc_prvdr_code',6)->nullable()->after('acc_prvdr_code');
        });

        DB::update("update account_stmts set acc_prvdr_code = 'CCA', lender_acc_prvdr_code = 'CCA', acc_number = '0703463210' where  account_id = 1783 and acc_prvdr_code is null and acc_number is null"); // ChapChap

        DB::update("update account_stmts set acc_prvdr_code = 'UEZM', lender_acc_prvdr_code = 'UEZM', acc_number = '20718971' where  account_id = 2895 and acc_prvdr_code is null and acc_number is null"); // EzeeMoney

        DB::update("update account_stmts set acc_prvdr_code = 'UMTN', lender_acc_prvdr_code = 'UMTN', acc_number = '810985' where  account_id = 4161 and acc_prvdr_code is null and acc_number is null"); // MTN Disbursement Account


        DB::update("update account_stmts set acc_prvdr_code = 'UMTN', lender_acc_prvdr_code = 'UEZM', acc_number = '797903' where  account_id = 3605 and acc_prvdr_code is null and acc_number is null"); // MTN Repayment for UEZM

        DB::update("update account_stmts set acc_prvdr_code = 'UMTN', lender_acc_prvdr_code = 'CCA', acc_number = '797904' where  account_id = 4094 and acc_prvdr_code is null and acc_number is null"); // MTN Repayment for CCA


        DB::update("update account_stmts set acc_prvdr_code = 'RBOK', lender_acc_prvdr_code = 'RBOK', acc_number = '100077653265' where  account_id = 4182 and acc_prvdr_code is null and acc_number is null"); // RBOK Disbursement Account

        DB::update("update account_stmts set acc_prvdr_code = 'RMTN', lender_acc_prvdr_code = 'RMTN', acc_number = '791519171' where  account_id = 4183 and acc_prvdr_code is null and acc_number is null"); // RMTN - Gasabo

        DB::update("update account_stmts set acc_prvdr_code = 'RMTN', lender_acc_prvdr_code = 'RMTN', acc_number = '791516469' where  account_id = 4184 and acc_prvdr_code is null and acc_number is null"); // RMTN - Nyarygenge

        DB::update("update account_stmts set acc_prvdr_code = 'RMTN', lender_acc_prvdr_code = 'RMTN', acc_number = '791334419' where  account_id = 4185 and acc_prvdr_code is null and acc_number is null"); // RMTN - Kicukiro

        DB::update("update account_stmts set acc_prvdr_code = 'UMTN', lender_acc_prvdr_code = 'UMTN', acc_number = '810986' where account_id = 3421 and acc_prvdr_code is null and acc_number is null"); // Old MTN Repayment Account

        
        DB::update("update account_stmts set updated_at = stmt_txn_date where updated_at is null and recon_status not in ('80_recon_done', '60_non_fa_credit')  and date(stmt_txn_date) >= '2022-01-01'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_stmts', function (Blueprint $table) {
            $table->dropColumn('review_reason');
            $table->dropColumn('acc_prvdr_code');
            $table->dropColumn('lender_acc_prvdr_code');
        });
    }
}
