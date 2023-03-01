<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Scripts\php\UpdateLastVisitDateScript;


class UpdateLastVisitBorrowers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        session()->put('country_code','UGA');
        $borrower = new UpdateLastVisitDateScript();
        $borrower->UpdateLastVisitDate();
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
