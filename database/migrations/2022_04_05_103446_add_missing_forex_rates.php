<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForexRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('UGX','EUR','0.0002540000','2022-01-31 00:00:00')");
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('UGX','USD','0.0002860000','2022-01-31 00:00:00')");
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('EUR','UGX','3931.1189640000','2022-01-31 00:00:00')");
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('EUR','USD','1.1231540000','2022-01-31 00:00:00')");
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('USD','UGX','3500.0717700000','2022-01-31 00:00:00')");
       DB::insert("INSERT INTO forex_rates (`base`,`quote`,`forex_rate`,`forex_date`) VALUES ('USD','EUR','0.8903500000','2022-01-31 00:00:00')");
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
