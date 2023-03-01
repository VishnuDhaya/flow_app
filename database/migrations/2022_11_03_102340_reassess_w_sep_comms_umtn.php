<?php

use App\Scripts\php\TempReassessCustomersScript;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReassessWSepCommsUmtn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_app_session('UGA');
        $temp_reassess = new TempReassessCustomersScript();
        $temp_reassess->temp_reassess_comms_customers('UMTN', 3000000, 3000000, False);
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
