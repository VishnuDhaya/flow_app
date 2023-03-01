<?php



namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\Schedule\ScheduleService;
use Log;
use App\Consts;
use App\Repositories\Eloquent\MarketRepository;
use App\Repositories\SQL\LeadRepositorySQL;

class DataConsentSctipt{

    public function update_data_consent(){

        $markets = (new ScheduleService())->get_markets_to_schedule();
        foreach($markets as $market){
            session()->put('country_code', $market->country_code);

            $borrowers = (new BorrowerRepositorySQL)->get_all_customers();
            foreach($borrowers as $borrower){

                if($borrower->lead_id){
                    $file_data_consent = (new LeadRepositorySQL)->get_file_data_consent($borrower->lead_id);
                    if($file_data_consent){
                        (new BorrowerRepositorySQL)->update_model_by_code(['cust_id' => $borrower->cust_id, "file_data_consent" => $file_data_consent ]);
                    }
                    
                }
                
            }

        }
    }
}