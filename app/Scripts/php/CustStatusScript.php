<?php



namespace App\Scripts\php;

use App\Repositories\SQL\BorrowerRepositorySQL;
use App\Services\BorrowerService;
use Log;
use App\Consts;

class CustStatusScript{

    public static function getCustStatus(){

        session()->put('country_code','UGA');
        $dp_code  = 'CCA';
        $borrower_repo = new BorrowerRepositorySQL();
        $borrowers = $borrower_repo->get_cca_customers($dp_code);

        foreach($borrowers as $borrower){
            $data = [
                "cust_id" => $borrower->cust_id,
                "country_code" => session('country_code'),
                "screen" => "view"
            ];
            $borr_serv = new BorrowerService();
            $result = $borr_serv->view($data);

            Log::warning("result");
            Log::warning(array($result));

            $customer[] = [
                'cust_id' => $borrower->cust_id,
                'middle_name' => $result->owner_person->middle_name,
                'kyc_status' => $result->kyc_status,
                'status' => $result->status,
                'national_id_path' => "http://app.flowglobal.net". $result->owner_person->photo_national_id_path."/". $result->owner_person->photo_national_id,

            ];
            // $customer['cust_id'] = $borrower->cust_id;
            // $customer['middle_name'] = $borrower->cust_id;
            // $customer['kyc_status'] = $borrower->cust_id;
            // $customer['status'] = $borrower->cust_id;
            // $customer['national_id_path'] = $borrower->cust_id;
        }
        return collect($customer);
    }
}