<?php

use App\Imports\UMTNCommissionImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportUmtnCommissionNov21Jun22 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        // set_app_session('UGA');
        // $comms_import = new UMTNCommissionImport;
        // $file_name = 'UMTN Comms ( Nov 2021 - Jul 2022 ).csv';
        // $comms_import->main($file_name);
        // $comms_import->update_acc_numbers();

        DB::unprepared(file_get_contents('storage/data/cust_commissions_nov21_jul22.sql'));

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
