<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBorrower extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('biz_name',80)->nullable();
            $table->string('remarks', 100)->nullable();
            $table->string('cust_id',20)->nullable();
            $table->string('master_cust_id',20)->nullable();
            $table->string('data_prvdr_cust_id',20)->nullable();
            $table->string('biz_type',30)->nullable();            
            $table->string('biz_addr_prop_type',50)->nullable();
            $table->string('data_prvdr_code',4)->nullable();
            $table->string('lender_code',4)->nullable();
            $table->string('photo_shop',50)->nullable();
            $table->unsignedInteger('org_id')->nullable();
            $table->unsignedInteger('biz_address_id')->nullable();
            $table->unsignedInteger('owner_person_id')->nullable();
            $table->unsignedInteger('owner_address_id')->nullable();
            $table->unsignedInteger('contact_person_id')->nullable();
            $table->unsignedInteger('flow_rel_mgr_id')->nullable();
            $table->unsignedInteger('dp_rel_mgr_id')->nullable();
            $table->smallInteger("number_of_tills")->nullable();
            $table->unsignedInteger('tot_loan_appls')->nullable()->default(0);
            $table->unsignedInteger('tot_loans')->nullable()->default(0);
            $table->unsignedInteger('tot_default_loans')->nullable()->default(0);
            $table->date('first_loan_date')->nullable();
            $table->string('status',10)->nullable()->default('enabled');
            $table->unsignedInteger('created_by')->nullable();   // Need to add reference?
            $table->unsignedInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrower');
    }
}
