<?php

use App\Imports\UMTNCommissionImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportUmtnCommsSep2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ini_set('memory_limit', '256M');
        set_app_session('UGA');
        $comms_import = new UMTNCommissionImport;
        DB::unprepared(file_get_contents('storage/data/comms/cust_commissions_sep22.sql'));
        $comms_import->update_acc_numbers();
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
