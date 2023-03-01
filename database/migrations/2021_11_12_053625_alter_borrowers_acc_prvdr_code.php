<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterBorrowersAccPrvdrCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        Schema::table('kyc_rules', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        Schema::table('loan_products', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        Schema::table('master_agreements', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        Schema::table('risk_category_rules', function (Blueprint $table) {
            $table->string('acc_prvdr_code', 6)->nullable()->after("data_prvdr_code");
        });

        DB::update("update borrowers set acc_prvdr_code = data_prvdr_code");
        DB::update("update borrowers set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update commissions set acc_prvdr_code = data_prvdr_code");
        DB::update("update commissions set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update kyc_rules set acc_prvdr_code = data_prvdr_code");
        DB::update("update kyc_rules set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update loan_applications set acc_prvdr_code = data_prvdr_code");
        DB::update("update loan_applications set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update loan_products set acc_prvdr_code = data_prvdr_code");
        DB::update("update loan_products set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update loans set acc_prvdr_code = data_prvdr_code");
        DB::update("update loans set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update master_agreements set acc_prvdr_code = data_prvdr_code");
        DB::update("update master_agreements set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        DB::update("update risk_category_rules set acc_prvdr_code = data_prvdr_code");
        DB::update("update risk_category_rules set acc_prvdr_code = 'UMTN' where acc_prvdr_code = 'UFLO'");

        Schema::table('borrowers', function (Blueprint $table) {
            $table->unsignedInteger('cust_acc_id')->nullable()->after("terminal_id");
            $table->string('acc_number')->nullable()->after("cust_acc_id");
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn('lender_data_prvdr_code', 'lender_acc_prvdr_code');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->string('acc_number', 150)->nullable()->after("cust_acc_id");
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('acc_number', 150)->nullable()->after("cust_acc_id");
        });

        Schema::table('cust_agreements', function (Blueprint $table) {
            $table->string('acc_number', 150)->nullable()->after("aggr_doc_id");
            $table->string('duration_type', 20)->change();
        });

        Schema::table('master_agreements', function (Blueprint $table) {
            $table->string('duration_type', 20)->change();
        });

        DB::update("update persons set associated_with = 'acc_prvdr' where associated_with = 'data_prvdr'");
        DB::update("update persons set associated_entity_code = 'UMTN' where associated_entity_code = 'UFLO'");

        DB::update("update accounts set lender_acc_prvdr_code = 'UMTN' where id = 3421");
        
        DB::update("update loans l join accounts a on a.id = l.cust_acc_id set l.acc_number = a.acc_number where a.id != 2628");

        DB::update("update loan_applications l join accounts a on a.id = l.cust_acc_id set l.acc_number = a.acc_number where a.id != 2628");

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
