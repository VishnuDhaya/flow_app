<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('loan_applications', function (Blueprint $table) {
        $table->increments('id');
        $table->string('country_code',4)->nullable();
        $table->string('loan_appl_doc_id', 50)->nullable()->unique();
        $table->unsignedInteger('loan_applied_by')->nullable();
        $table->string('loan_approver_name', 50)->nullable();
        $table->unsignedInteger('loan_approver_id')->nullable();  
        $table->dateTime('loan_appl_date')->nullable();
        $table->dateTime('loan_approved_date')->nullable();
        $table->string('photo_first_appl',50)->nullable();
        //$table->unsignedInteger('loan_approved_by')->nullable();
        $table->unsignedInteger('dp_rel_mgr_id')->nullable();
        $table->unsignedInteger('flow_rel_mgr_id')->nullable();
        $table->string('lender_code', 4)->nullable();
        $table->string('data_prvdr_code', 4)->nullable();
        $table->string('cust_name', 50)->nullable();
        $table->string('cust_id', 50)->nullable();
        $table->string('cust_addr_text', 500)->nullable();
        $table->string('cust_mobile_num',20)->nullable();
        $table->unsignedInteger('cust_acc_id')->nullable();
        $table->unsignedInteger('product_id')->nullable();
        $table->string('product_name', 50)->nullable();
        $table->double('loan_principal', 15,2)->nullable();
        $table->smallInteger('duration')->nullable();
        $table->string('flow_fee_type',10)->nullable();
        $table->double('flow_fee' ,15,2)->nullable();
        $table->string('flow_fee_duration',20)->nullable();
        $table->double('due_amount',15,2)->nullable();
        $table->string('currency_code',3)->nullable();
        $table->unsignedInteger('cs_model_id')->nullable();
        $table->smallInteger('credit_score')->nullable();
        $table->boolean('customer_consent_rcvd')->nullable();
        $table->string('action_reason_code', 40)->nullable();

        $table->string('remarks',100)->nullable();
        $table->unsignedInteger('tot_loan_appls')->nullable();
        $table->unsignedInteger('tot_loans')->nullable();
        $table->unsignedInteger('tot_default_loans')->nullable();
        $table->date('first_loan_date')->nullable();
        $table->string('status',20)->nullable();
        $table->unsignedInteger('created_by')->nullable();   // Need to add reference?
        $table->unsignedInteger('updated_by')->nullable(); 
        
       
  $table->dateTime('created_at')->nullable();
  $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_applications');
    }
}
