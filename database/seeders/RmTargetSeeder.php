<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RmTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 1006, 'JUSTINE BABIRYE', 2022,'{\"Jan\":30,\"Feb\":50,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":30,\"Aug\":30,\"Sep\":0}')");							
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2427, 'HENRY SIKYOMU', 2022, '{\"Jan\":30,\"Feb\":60,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 1742, 'MATIA TOMUSANGE', 2022,'{\"Jan\":30,\"Feb\":60,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2440, 'RITAH NABWIRE', 2022,'{\"Jan\":25,\"Feb\":60,\"Mar\":75,\"Apr\":75,\"May\":75,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2439, 'ASIYA NAKATO', 2022,'{\"Jan\":30,\"Feb\":50,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2560, 'FLAVIA NAMUTEBI', 2022,'{\"Jan\":30,\"Feb\":50,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2561, 'MAJID TUGUME', 2022,'{\"Jan\":25,\"Feb\":60,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2562, 'ONESMUS AHIMBISIBWE', 2022,'{\"Jan\":30,\"Feb\":60,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2575, 'PATRICK ASIIMWE', 2022,'{\"Jan\":30,\"Feb\":60,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2509, 'CYNTHIA ARYATUHA', 2022,'{\"Jan\":30,\"Feb\":50,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2707, 'BEN OPOLOT', 2022,'{\"Jan\":30,\"Feb\":60,\"Mar\":80,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":40,\"Aug\":40,\"Sep\":40}')");				
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 2709, 'ANDREW OKUKU WAFULA', 2022,'{\"Jan\":30,\"Feb\":50,\"Mar\":75,\"Apr\":80,\"May\":80,\"Jun\":75,\"Jul\":50,\"Aug\":50,\"Sep\":40}')");															
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 3150, 'EMMANUEL OKII', 2022,'{\"Jan\":30,\"Feb\":60,\"Mar\":50,\"Apr\":75,\"May\":75,\"Jun\":75,\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 3461, 'OENEN NATHAN', 2022, '{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 3462, 'WAISWA HAKIM', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 3463, 'AKOT FILDA', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 3464, 'AKONU EMMANUEL', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('RWA', 3568, 'Vivianne Kamikazi', 2022,'{\"Apr\":30,\"May\":60,\"Jun\":60,\"Jul\":60,\"Aug\":60,\"Sep\":50}')");
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('RWA', 3948, 'JOEL MUGISHA', 2022,'{\"Jun\":60,\"Jul\":60,\"Aug\":60,\"Sep\":50}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('RWA', 3951, 'ISMAIL RWIBUTSO', 2022,'{\"Jun\":60,\"Jul\":60,\"Aug\":60,\"Sep\":50}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('RWA', 3939, 'NADINE BLESSINGS UWIMANA', 2022,'{\"Jun\":60,\"Jul\":60,\"Aug\":60,\"Sep\":50}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4081, 'YUSUF ISIFU OMURWON', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4082, 'CHRISTOPHER TUMWEBAZE', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4084, 'SEZI WANZUSI', 2022,'{\"Jul\":50,\"Aug\":50,\"Sep\":40}')");								
        
        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4086, 'CISSY NANKWANGA', 2022,'{\"Jul\":40,\"Aug\":40,\"Sep\":40}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4928, 'CAROLINE AYEBARE', 2022,'{\"Sep\":40}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('RWA', 4611, 'YVES NKURIKIYIMFURA', 2022,'{\"Sep\":50}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4922, 'JOSHUA OMASET', 2022,'{\"Sep\":40}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4923, 'DANIEL OKELLO', 2022,'{\"Sep\":40}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4926, 'ISSA UMARY BALINDA', 2022,'{\"Sep\":40}')");

        DB::insert("insert into rm_targets (country_code, rel_mgr_id, rm_name, year, targets) values ('UGA', 4927, 'ELIAS TWINOMUJUNI', 2022,'{\"Sep\":40}')");

    }
}