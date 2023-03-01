<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTablePreApprovals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->string('cust_id',20)->nullable();
            $table->unsignedInteger('flow_rel_mgr_id')->nullable();
            $table->unsignedInteger('appr_count')->nullable();
            $table->dateTime('appr_start_date')->nullable();
            $table->dateTime('appr_exp_date')->nullable();
            $table->string('status',20)->nullable();
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
        //
    }
}
