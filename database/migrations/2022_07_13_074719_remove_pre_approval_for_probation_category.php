<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePreApprovalForProbationCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_app_session('UGA');

		$results = DB::select("select b.cust_id from borrowers b, pre_approvals p where p.appr_count > 0 and p.status = 'enabled' and p.cust_id = b.cust_id and b.tot_loans <= 5 and b.category = 'Probation'");

        foreach($results as $result){
            $rm_serv = new \App\Services\Mobile\RMService();
            $data['cust_id'] = $result->cust_id;
            $rm_serv->remove_pre_approval($data);
        }
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
