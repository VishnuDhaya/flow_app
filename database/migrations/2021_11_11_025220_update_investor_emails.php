<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class UpdateInvestorEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('investor_users')->where('person_id', '=', 2531)->update(['email' => 'aamaasen@gmail.com']);
        DB::table('persons')->where('id', '=', 2531)->update(['email_id' => 'aamaasen@gmail.com']);

        DB::table('investor_users')->where('person_id', '=', 2532)->update(['email' => 'robert.dewanger@chello.at']);
        DB::table('persons')->where('id', '=', 2532)->update(['email_id' => 'robert.dewanger@chello.at']);

        DB::table('investor_users')->where('person_id', '=', 2533)->update(['email' => 'K.Sternisko@hotmail.de']);
        DB::table('persons')->where('id', '=', 2533)->update(['email_id' => 'K.Sternisko@hotmail.de']);

        DB::table('investor_users')->where('person_id', '=', 2534)->update(['email' => 'sternbergac@gmail.com']);
        DB::table('persons')->where('id', '=', 2534)->update(['email_id' => 'sternbergac@gmail.com']);

        DB::table('investor_users')->where('person_id', '=', 2535)->update(['email' => 'mccauley_james@hotmail.com']);
        DB::table('persons')->where('id', '=', 2535)->update(['email_id' => 'mccauley_james@hotmail.com']);

        DB::table('investor_users')->where('person_id', '=', 2536)->update(['email' => 'w.ryll@yahoo.de']);
        DB::table('persons')->where('id', '=', 2536)->update(['email_id' => 'w.ryll@yahoo.de']);
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
