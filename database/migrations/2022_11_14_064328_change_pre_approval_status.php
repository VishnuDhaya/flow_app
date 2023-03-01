<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;




class ChangePreApprovalStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()

    {
        $date  = Carbon::yesterday()->format(Consts::DB_DATE_FORMAT);
        
        DB::update("update pre_approvals set status = 'disabled' where status = 'disbled'");

        DB::update("update pre_approvals set status = 'disabled' where date(appr_exp_date) <= '$date'");


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
