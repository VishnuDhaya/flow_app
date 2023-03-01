<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestTransformer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $req_mapper = ['mob' => 'mobile_num', 'cc' => 'country_code', 'c_id' => 'cust_id', 'fa_id' => 'loan_doc_id', 'amt' => 'loan_principal', 'dis_dt' => 'disbursal_date',
            'pay_dt' => 'paid_date', 'due_dt' => 'due_date', 'dur' => 'duration', 'fee' => 'flow_fee', 'fa_appl_id' => 'loan_appl_doc_id', 'appl_dt' => 'loan_appl_date',
            'ap_code' => 'acc_prvdr_code', 'ac_num' => 'acc_number', 'pnlty' => 'penalty_amount', 'ac_id' => 'account_id', 'ap_name' => 'acc_prvdr_name', 'prdct_id' => 'product_id',
            'f_type' => 'flow_fee_type', 'ap_logo' => 'acc_prvdr_logo','accs' => 'accounts', 'wlt' => 'wallet', 'tx_dt' => 'txn_date', 'pmts' => 'payments','paid_amt' => 'paid_amount',
            'hldr' => 'holder_name', 'repay_amt' => 'repayment_amount','pty_days' => 'penalty_days','prdct_pt' => 'product_penalty','tot_pt' => 'tot_penalty','prvtnl_pnlty' => 'provisional_penalty',
            'bz_nm' => 'biz_name','p_shp' => 'photo_shop', 'bz_type' => 'biz_type', 'owner_id' => 'owner_person_id', 'bz_addr_id' => 'biz_address_id', 'own' => 'owner_person', 'f_nm' => 'first_name',
            'm_nm' => 'middle_name', 'l_nm' => 'last_name', 'gndr' => 'gender', 'nat_id' => 'national_id', 'alt_num_1' => 'alt_biz_mobile_num_1', 'alt_num_2' => 'alt_biz_mobile_num_2', 'wtsp' => 'whatsapp',
            'mail' => 'email_id', 'addr' => 'biz_address', 'tot_fa' => 'tot_loans','ontime' => 'ontime_loans', 'upgrd' => 'prdct_to_upgrade', 'crnt_fa_lmt' => 'crnt_fa_limit','upgrd_amts' => 'upgradable_amounts',
            'rqstd_amt' => 'requested_amount', 'd_key' => 'data_key', 'd_val' => 'data_value', 'd_code' => 'data_code', 'sch_dt' => 'sch_date', 'sch_prps' => 'sch_purpose', 'vst_prps' => 'visit_purpose', 
            'resch_dt' => 'resch_date', 'vst_s_time' => 'visit_start_time', 'vst_e_time' => 'visit_end_time', 'rmrks' => 'remarks'];
            
        if($request->data){
            $request->data = (array) $this->transform($request->data,$req_mapper);
        }
        $resp = $next($request);
        $resp_mapper = array_flip($req_mapper);
        $resp = (array)$resp->getData();
        $resp = (array)$this->transform($resp,$resp_mapper);
        $headers = get_header(request()->headers->get('origin'));
        $resp =  Response::json($resp, $resp['status_code'], $headers);

        return $resp;
    }

    public function transform($data,$mapper){
        foreach ($data as $key => $val) {
            if(gettype($val) == "string"){
                $val = str_replace(" 00:00:00","",$val);
            }
            if(is_object($val)){
                $val = (array)$val;
            }
            if (is_array($val)) {
                m_array_filter($val);
                if (isset($mapper[$key])) {
                    $data[$mapper[$key]] = $this->transform($val,$mapper);
                    unset($data[$key]);
                }
                else{
                    $data[$key] = $this->transform($val,$mapper);
                }
            }
            else{
                if (isset($mapper[$key])) {
                    $data[$mapper[$key]] = $val;
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
}
