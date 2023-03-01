<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBorrowersAddRequireRmApproval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	 Schema::table('borrowers', function (Blueprint $table) {
	    $table->boolean('is_og_loan_overdue')->nullable(true)->after('first_loan_date');
        $table->string('ongoing_loan_doc_id', 50)->nullable(true)->after('first_loan_date');
        $table->string('pending_loan_appl_doc_id', 50)->nullable(true)->after('first_loan_date');
        $table->date('addl_appr_until')->nullable(true)->after('first_loan_date');
        $table->boolean('require_addl_appr')->nullable(true)->after('first_loan_date');
	    $table->integer('csf_run_id')->nullable(true)->after('first_loan_date');
	    
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('borrowers', function (Blueprint $table) {
		$table->dropColumn('require_addl_appr');
        $table->dropColumn('addl_appr_until');
		$table->dropColumn('ongoing_loan_doc_id');
        $table->dropColumn('pending_loan_appl_doc_id');
        $table->dropColumn('is_og_loan_overdue');
        $table->dropColumn('csf_run_id');
	    });
    }
}
