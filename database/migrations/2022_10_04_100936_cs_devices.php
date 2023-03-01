<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CsDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'UGA';
        $cur_date =  Carbon::now()->format('Y-m-d');
        
        DB::table('cs_devices')->insert([
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"sip",'number'=>'agent3.flow@ug.sip.africastalking.com','person_id'=>2436,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"sip",'number'=>'agent2.flow@ug.sip3.africastalking.com','person_id'=>2204,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"sip",'number'=>'agent1.flow@ug.sip3.africastalking.com','person_id'=>2437,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"sip",'number'=>'agent2.flow@ug.sip.africastalking.com','person_id'=>1486,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"mobile",'number'=>'+256750554558 ','person_id'=>1486,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"mobile",'number'=>'+256772325838','person_id'=>1486,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"mobile",'number'=>'+256759625559','person_id'=>1486,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
           ['country_code'=>$country_code,'date'=> $cur_date,'type'=>"mobile",'number'=>'+256776909989','person_id'=>1486,'call_status'=>"available",'status'=>"disabled",'created_at'=>now()],
        ]);
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
