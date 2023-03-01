<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvestorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $persons = [
                        [['first_name'=>"Dennis", "last_name"=>"Krings-Ernst", "email_id"=>"dennis@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>"",'fund_code'=>'VC-MAY21-USD','inv_amount'=>25000,'inv_currency_code'=>'USD','invested_date'=>'2021-04-09', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Laurenz", "last_name"=>"Apius", "email_id"=>"laurenz@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1002,'fund_code'=>'VC-MAY21-EUR','inv_amount'=>25000,'inv_currency_code'=>'EUR','invested_date'=>'2021-04-10', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Asmund", "last_name"=>"Aamas", "email_id"=>"asmund@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1003,'fund_code'=>'FC-JUN21-EUR-A','inv_amount'=>10000,'inv_currency_code'=>'EUR','invested_date'=>'2021-04-16', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Robert", "last_name"=>"Dewanger", "email_id"=>"robert@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1004,'fund_code'=>'FC-JUN21-EUR-A','inv_amount'=>10000,'inv_currency_code'=>'EUR','invested_date'=>'2021-04-27', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Konrad", "last_name"=>"Sternisko", "email_id"=>"konrad@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1005,'fund_code'=>'FC-JUN21-EUR-A','inv_amount'=>10000,'inv_currency_code'=>'EUR','invested_date'=>'2021-04-30', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Andrea", "last_name"=>"Sternberg", "email_id"=>"andrea@flowglobal.net", "gender"=>"Female", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1006,'fund_code'=>'FC-JUN21-USD','inv_amount'=>10000,'inv_currency_code'=>'USD','invested_date'=>'2021-04-29', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"James", "last_name"=>"Mccauley", "email_id"=>"james@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1007,'fund_code'=>'FC-JUN21-EUR-B','inv_amount'=>10000,'inv_currency_code'=>'EUR','invested_date'=>'2021-05-07', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Wolfgang", "last_name"=>"Ryll", "email_id"=>"wolfgang@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1008,'fund_code'=>'FC-JUN21-EUR-B','inv_amount'=>10000,'inv_currency_code'=>'EUR','invested_date'=>'2021-05-17', 'created_at'=> Carbon::now()]],
                        [['first_name'=>"Salim", "email_id"=>"salim@flowglobal.net", "gender"=>"Male", "mobile_num"=>"", "whatsapp"=>"", "national_id"=>"", "designation"=>"investor", "dob"=>" 2021-09-28", "role"=>"investor", "create_user"=>true, "country_code"=>"UGA"],
                            ['person_id'=>1009,'fund_code'=>'VC-JUN21-EUR','inv_amount'=>25000,'inv_currency_code'=>'EUR','invested_date'=>'2021-06-09', 'created_at'=> Carbon::now()]]
            ];

        session()->put('country_code','*');
        $serv = new \App\Services\CommonService();
        try{
            DB::beginTransaction();
            foreach($persons as $person) {
                $id = $serv->create_person($person[0]);
                $person[1]['person_id'] = $id;
                DB::table('investments')->insert($person[1]);
            }
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            thrw($e);
        }

    }
}
