<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBorrowLastLoanDocId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #DB::select("select s.cust_id,l.loan_doc_id  from (select cust_id, max(id) as id from loans where status = 'settled' group by cust_id) s inner join loans l on l.id = s.id");
        DB::update("create temporary table temp_loan_tbl select s.cust_id,l.loan_doc_id  from (select cust_id, max(id) as id from loans where status = 'settled' group by cust_id) s inner join loans l on l.id
        = s.id");
        DB::update("update borrowers b join temp_loan_tbl t on b.cust_id = t.cust_id set b.last_loan_doc_id = t.loan_doc_id");
       # DB::update("drop table temp_loan_tbl");
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
