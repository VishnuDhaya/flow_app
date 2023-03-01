<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertCoreUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     
    public function up()
    {
    DB::table('persons')->insert([['first_name' => 'Nitin','last_name'=> 'Nitin','initials' => 'N','dob' => '1993-12-22','gender' => 'female','whatsapp' => '700683308','email_id' => 'nitin123@flowglobal.net','mobile_num' => '700683308','phone_num' => '700683308','designation' => ' ','country_code' => 'UGA','national_id'=> '']]);
    $person = DB::selectOne("select id from persons order by id desc");
    DB::table('core_users')->insert([['email' => 'nitin123@flowglobal.net','password' => bcrypt('password'),  'role_codes' => 'customer','belongs_to' => 'FLOW', 'belongs_to_code' => 'FLW', 'status' => 'enabled', 'country_code' => 'UGA', 'cust_id'=>"12345",'mobile_number'=>"9994870894",'person_id' => $person->id]]);
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
