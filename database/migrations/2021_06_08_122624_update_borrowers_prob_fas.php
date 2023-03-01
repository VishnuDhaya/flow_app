<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Scripts\php\FaCountBackTrackScript;

class UpdateBorrowersProbFas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        session()->put('country_code','UGA');
        $borrower = new FaCountBackTrackScript();
        $borrower->updateFACount();
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
