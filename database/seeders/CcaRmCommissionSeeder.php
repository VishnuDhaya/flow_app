<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;

class CcaRmCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('commissions')->insert([
            ['rm_name' => "Hussein Ntabazi", 'rm_id' => 1227, 'month' => 202105, 'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 0, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 250000, 'country_code' => 'UGA'],
            ['rm_name' => "Hamis Kirumba", 'rm_id' => 1237, 'month' => 202105 ,'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 0, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 0, 'country_code' => 'UGA'],
            ['rm_name' => "Ssemanda Timothy", 'rm_id' => 1238, 'month' => 202105, 'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 85000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 85000, 'country_code' => 'UGA'],
            ['rm_name' => "Omome George william", 'rm_id' => 1241, 'month' => 202105, 'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 98000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 98000, 'country_code' => 'UGA'],
            ['rm_name' => "Evans Senkindu", 'rm_id' => 1240 , 'month' => 202105, 'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 0, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 250000,  'country_code' => 'UGA'],
            ['rm_name' => "Ojara Peter Otto", 'rm_id' => 1242, 'month' => 202105, 'cust_acquisition_comm' => 0, 'ontime_repay_comm' => 32000 , 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 32000, 'country_code' => 'UGA'],

            ['rm_name' => 'Hussein Ntabazi', 'rm_id' => 1227, 'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 0, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 250000, 'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba', 'rm_id' => 1237, 'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 76000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 76000, 'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy', 'rm_id' => 1238, 'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 97000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 97000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william', 'rm_id' => 1241, 'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 100000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 100000,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu', 'rm_id' => 1240,  'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 0, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 250000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto', 'rm_id' => 1242, 'month' => 202104, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 37000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 37000,  'country_code' => 'UGA'],

['rm_name' => 'Hussein Ntabazi', 'rm_id' => 1227,'month' => 202103, 'cust_acquisition_comm' =>65000 , 'ontime_repay_comm' => 730000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 1045000,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba', 'rm_id' => 1237,'month' => 202103, 'cust_acquisition_comm' =>5000 , 'ontime_repay_comm' => 112000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 117000,  'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy', 'rm_id' => 1238,'month' => 202103, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 119000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 119000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william', 'rm_id' => 1241,'month' => 202103, 'cust_acquisition_comm' =>15000 , 'ontime_repay_comm' => 120000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 135000,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu', 'rm_id' => 1240, 'month' => 202103, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 0, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 250000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto', 'rm_id' => 1242,'month' => 202103, 'cust_acquisition_comm' =>20000 , 'ontime_repay_comm' => 33000, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 53000,  'country_code' => 'UGA'],
            
['rm_name' => 'Hussein Ntabazi', 'rm_id' => 1227, 'month' => 202102, 'cust_acquisition_comm' =>130000 , 'ontime_repay_comm' => 390400, 'facilitation_comm' => 250000,'agent_of_the_month_comm' => 0, 'total_paid' => 770400,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba', 'rm_id' => 1237, 'month' => 202102, 'cust_acquisition_comm' =>5000 , 'ontime_repay_comm' => 98846, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 103846,  'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy', 'rm_id' => 1238, 'month' => 202102, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 68000, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 68000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william', 'rm_id' => 1241, 'month' => 202102, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 64533, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 64533,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu', 'rm_id' => 1240 , 'month' => 202102, 'cust_acquisition_comm' =>45000 , 'ontime_repay_comm' => 198000, 'facilitation_comm' => 250000,'agent_of_the_month_comm' => 0, 'total_paid' => 493000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto', 'rm_id' => 1242, 'month' => 202102, 'cust_acquisition_comm' =>0 , 'ontime_repay_comm' => 19360, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 19360,  'country_code' => 'UGA'],

['rm_name' => 'Hussein Ntabazi','rm_id' => 1227,'month' => 202012, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 866000, 'facilitation_comm' => 250000,'agent_of_the_month_comm' => 0, 'total_paid' => 1216000,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba','rm_id' => 1237,'month' => 202012, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 188000, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 188000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto','rm_id' => 1242,'month' => 202012, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 34000, 'facilitation_comm' => 0,'agent_of_the_month_comm' => 0, 'total_paid' => 34000,  'country_code' => 'UGA'],

['rm_name' => 'Hussein Ntabazi','rm_id' => 1227, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 735000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 100000,'total_paid' => 1085000,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba','rm_id' => 1237, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 99000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 99000,  'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy','rm_id' => 1238, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 189000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 189000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william','rm_id' => 1241, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 168000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 168000,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu','rm_id' => 1240 ,'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 1296000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 1546000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto','rm_id' => 1242, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 35000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 35000,  'country_code' => 'UGA'],
['rm_name' => 'Ali Kassim', 'rm_id' => 1244, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 613000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 863000,  'country_code' => 'UGA'],
['rm_name' => 'Obette Emanuel','rm_id' => null, 'month' => 202011, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 18000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 18000,  'country_code' => 'UGA'],

['rm_name' => 'Hussein Ntabazi','rm_id' => 1227, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 515000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 765000,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba','rm_id' => 1237, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 108000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 108000,  'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy','rm_id' => 1238, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 152000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 152000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william','rm_id' => 1241, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 152000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 152000,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu','rm_id' => 1240 ,'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 487000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 737000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto','rm_id' => 1242, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 47000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 47000,  'country_code' => 'UGA'],
['rm_name' => 'Ali Kassim','rm_id' => 1244, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 638000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 100000,'total_paid' => 988000,  'country_code' => 'UGA'],
['rm_name' => 'Obette Emanuel','rm_id' => null, 'month' => 202010, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 24000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 24000,  'country_code' => 'UGA'],

['rm_name' => 'Hussein Ntabazi','rm_id' => 1227, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 259000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 0,'total_paid' => 509000,  'country_code' => 'UGA'],
['rm_name' => 'Hamis Kirumba','rm_id' => 1237, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 118000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 118000,  'country_code' => 'UGA'],
['rm_name' => 'Ssemanda Timothy','rm_id' => 1238, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 180000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 180000,  'country_code' => 'UGA'],
['rm_name' => 'Omome George william','rm_id' => 1241, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 162000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 162000,  'country_code' => 'UGA'],
['rm_name' => 'Evans Senkindu','rm_id' => 1240 ,'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 218000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 218000,  'country_code' => 'UGA'],
['rm_name' => 'Ojara Peter Otto','rm_id' => 1242, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 57000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 57000,  'country_code' => 'UGA'],
['rm_name' => 'Ali Kassim','rm_id' => 1244, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 487000, 'facilitation_comm' => 250000, 'agent_of_the_month_comm' => 100000,'total_paid' => 837000,  'country_code' => 'UGA'],
['rm_name' => 'Obette Emanuel','rm_id' => null, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 22000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 22000,  'country_code' => 'UGA'],
['rm_name' => 'Ruth Kisakye','rm_id' => null, 'month' => 202009, 'cust_acquisition_comm' => 0 , 'ontime_repay_comm' => 5000, 'facilitation_comm' => 0, 'agent_of_the_month_comm' => 0,'total_paid' => 0,  'country_code' => 'UGA'],


        ]);
    }
}
