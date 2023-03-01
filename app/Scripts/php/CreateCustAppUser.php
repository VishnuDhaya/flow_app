<?php
namespace App\Scripts\php;

use App\Services\BorrowerService;


class CreateCustAppUser{

    public function generate_user()
    {
        session()->put('user_id', 0);
        session()->put('country_code', 'UGA');
        $mob_num_arr = ['758666619', '782751991', '781882719', '775655248', '787400404', '705495304', '702223719', '703978883', '774356670', '779404819', '772165968', '781196684', '777391602', '775695960', '789823914', '777702263', '779221521', '788822097', '783178188', '776922544'];

        foreach ($mob_num_arr as $mobile_num){
            $cust_id = gen_cust_id_frm_mob_num($mobile_num);
            (new BorrowerService())->set_cust_app_access($cust_id, 'enabled');
        }
    }


}