<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLoanApplLoanDocId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update loan_applications join loans on loan_applications.id = loans.loan_appl_id set loan_applications.loan_doc_id = loans.loan_doc_id where loan_applications.status = 'approved'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
