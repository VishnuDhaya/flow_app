<?php

use App\Models\CsFactorValues;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMonthlyCommsLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_app_session('UGA');
        
        $cs_factor_repo = new CsFactorValues;
        $lower_limit = $cs_factor_repo->get_record_by_many(['csf_group', 'csf_type', 'value_from', 'value_to'], ['avg_comms', 'monthly_comms', 0, 80000]);
        $upper_limit = $cs_factor_repo->get_record_by_many(['csf_group', 'csf_type', 'value_from', 'value_to'], ['avg_comms', 'monthly_comms', 80000, 999999999]);

        $cs_factor_repo->update_model(['id'=>$lower_limit->id, 'value_to' => 60000]);
        $cs_factor_repo->update_model(['id'=>$upper_limit->id, 'value_from' => 60000]);
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
