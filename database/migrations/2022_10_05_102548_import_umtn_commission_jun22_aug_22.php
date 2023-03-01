<?php

use App\Imports\UMTNCommissionImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportUmtnCommissionJun22Aug22 extends Migration
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
        $file_name = 'UMTN Comms ( Jun 2022 - Aug 2022 ).csv';
        $comms_import->main($file_name);
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
