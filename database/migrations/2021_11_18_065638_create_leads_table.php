<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->json('cust_reg_json')->nullable();
            $table->json('lead_json')->nullable();
            $table->json('cust_eval_json')->nullable();
            $table->string('status',32)->nullable();
            $table->dateTime('lead_date')->nullable();
            $table->dateTime('eval_date')->nullable();
            $table->dateTime('rm_kyc_start_date')->nullable();
            $table->dateTime('rm_kyc_end_date')->nullable();
            $table->dateTime('audit_kyc_start_date')->nullable();
            $table->dateTime('audit_kyc_end_date')->nullable();
            $table->unsignedInteger('rm_eval_id')->nullable();
            $table->string('mobile_num', 10)->nullable();
            $table->string('biz_name',80)->nullable();
            $table->string('acc_prvdr_code',4);
            $table->string('account_num',32)->nullable(); 
            $table->string('location',32)->nullable(); 
            $table->string('territory', 32)->nullable();
            $table->string('product_group', 32);
            $table->unsignedInteger('flow_rel_mgr_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();   
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
        Schema::dropIfExists('leads');
    }
}
