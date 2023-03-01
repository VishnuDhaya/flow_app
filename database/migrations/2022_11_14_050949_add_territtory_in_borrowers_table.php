<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTerrittoryInBorrowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::update("update borrowers set territory = 'jinja' where id in (2363, 2365, 2378, 2458, 2594)");

        DB::update("update borrowers set territory = 'central' where id in (2361, 2368, 2397, 2417, 2441, 2570, 2582, 2589, 2618)");

        DB::update("update borrowers set territory = 'entebbe' where id in (2605, 2645)");
        
        DB::update("update borrowers set territory = 'kawempe' where id in (2373, 2393, 2400, 2584)");

        DB::update("update borrowers set territory = 'makindye' where id in (2408, 2611, 2646)");

        DB::update("update borrowers set territory = 'mbale' where id in (2571, 2585, 2619, 2626, 2627)");

        DB::update("update borrowers set territory = 'mbarara' where id in (2610, 2757)");

        DB::update("update borrowers set territory = 'mukono' where id in (2583, 2710)");

        DB::update("update borrowers set territory = 'nakawa' where id in (2398, 2432, 2588, 2713)");

        DB::update("update borrowers set territory = 'nsangi' where id  = 2349");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borrowers', function (Blueprint $table) {
            //
        });
    }
}
