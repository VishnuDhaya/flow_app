<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\SQL\PersonRepositorySQL;

class CreateAppUsersForReadOnlyNInvestor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code =  'UGA';
        session()->put('country_code', 'UGA');
        $person_repo = new PersonRepositorySQL();

        $last_id = $person_repo->get_last_id();


        DB::table('persons')->insert([
     
            ['first_name' => 'Read','last_name'=> 'Only','initials' => 'MR','dob' => '1997-08-10','gender' => 'MALE','whatsapp' => '919650088909','email_id' => 'read_only@consultcolors.com','mobile_num' => '919650088909','phone_num' => '','designation' => 'READONLYUSER','country_code' =>  $country_code,'national_id'=> 'Z4322369'],

            ['first_name' => 'Investor','last_name'=> 'Investor','initials' => 'MR','dob' => '1997-08-10','gender' => 'MALE','whatsapp' => '919650088967','email_id' => 'investor@consultcolors.com','mobile_num' => '919650088967','phone_num' => '','designation' => 'INVESTOR','country_code' =>  $country_code,'national_id'=> 'Z4322360']
        ]);

        DB::table('app_users')->insert([

            ['email' => 'read_only@consultcolors.com','password' => bcrypt('password'), 'role_codes' => 'read_only','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id],
            ['email' => 'investor@consultcolors.com','password' => bcrypt('password'), 'role_codes' => 'investor','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLOW', 'status' => 'enabled', 'country_code' =>  $country_code, 'person_id' => ++$last_id]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
