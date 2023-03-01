<?php
 namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Models\LoanProduct;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;

 class LoanProductRepositorySQL extends BaseRepositorySQL implements BaseRepositoryInterface{
    
 	public function __construct()
    {
      parent::__construct();

    }

 	public function model(){
			return LoanProduct::class;
	}

 	public function create(array $loan_product){
       
        $loan_product['loan_purpose'] = 'float_advance';
 		$duration =  $loan_product['flow_fee_duration'];
 		if($duration == "each_day"){
 			$loan_product['flow_fee_duration'] = 1;
 		}else if($duration == "entire_duration"){
 			$loan_product['flow_fee_duration'] =  $loan_product['duration'];
 		}
 		$product_id = parent::insert_model($loan_product);
 		return $product_id;
 	}

 	public function update(array $data){
        
        if(isset($data['product_name']) &&  isset($data['cs_model_code']) && isset($data['penalty_amount']) ){
            $result = parent::update_model($data);
            return $result; 
        }else{
            thrw("Cannot update empty value.");
        }
        
 	}

	public function list($id){
		throw new BadMethodCallException();	
	}

	public function delete($id){
		throw new BadMethodCallException();	
	}
	public function show_list(array $req){
        $addtn_sql = '';

        if(array_key_exists('acc_prvdr_code', $req)){
            $addtn_sql = " and a.acc_prvdr_code = '".$req['acc_prvdr_code']."'";
        }
		return DB::select("/*$this->api_req_id*/ select a.*,b.name acc_provider,c.name lender from  loan_products a,acc_providers b,lenders c where a.acc_prvdr_code=b.acc_prvdr_code and a.lender_code=c.lender_code and a.country_code=?  $addtn_sql order by a.status desc, cs_model_code, max_loan_amount, duration",[session('country_code')]);
	
    }
    public function get_product_with_ap($req){
        $acc_prvdr_code = $req['acc_prvdr_code'];
        $country_code = session('country_code');
        return DB::select("/*$this->api_req_id*/ select product_name as name,id from loan_products where country_code=? and acc_prvdr_code = ?",[$country_code, $req['acc_prvdr_code']]);
    }

 	public function get_products_by($field_names, $field_values, $agrmt_for = null, $limit_amt = 0)
    {
        $addl_sql = "";
        if($agrmt_for == "onboarded"){
            // $addl_sql = " and product_type != 'probation' ";
            $addl_sql .= " and product_type not in ('probation','float_vending')";
        }else if($agrmt_for != null){
            $addl_sql .= " and product_type = '$agrmt_for'";
        }else{
            $addl_sql .= "";
        }

        if($limit_amt > 0){
            $addl_sql .= " and max_loan_amount <= '$limit_amt'";
        }

    	$fields_arr=['id', 'product_code', 'product_name', 'max_loan_amount', 'duration', 'flow_fee_duration', 'flow_fee_type', 'flow_fee', 'cs_model_code', 'product_type','status','penalty_amount'];
        $result = parent::get_records_by_many($field_names, $field_values,$fields_arr, " and ", $addl_sql);
    	return $result;
    }

    public function get_penalty_amount($product_id){
        $result = DB::selectOne("/*$this->api_req_id*/ select penalty_amount from loan_products where id = ? limit 1",[$product_id]);
        if($result){
            return $result->penalty_amount;
        }
        return 0;
    }
    
    public function get_products($product_ids)
    {
        $loan_products = DB::table('loan_products')->where('country_code',"UGA")->whereIn('id',$product_ids)->get();
        return $loan_products;
    }
    public function get_loan_product($product_id,$fields = ['id', 'status', 'product_name','cs_model_code','product_type','duration','max_loan_amount','flow_fee','penalty_amount','flow_fee_duration','flow_fee_type','loan_purpose'])
    {
        $fields = implode(", ",$fields);
        return DB::selectOne("/*$this->api_req_id*/ select {$fields} from loan_products where id = ? limit 1",[$product_id]);

    }
   public function getloanproduct($product_id)
   {
     return DB::selectOne("/*$this->api_req_id*/ select * from loan_products where id = ? limit 1",[$product_id]);
   }
   public function get_tf_product($val){
         $prdct = explode('/',$val);
       $prdctrepo = new LoanProductRepositorySQL();
       Log::warning($prdct);
       $prdctquery = $prdctrepo->get_record_by_many(['loan_purpose','cs_model_code','status','product_code'],['terminal_financing','tf_products','enabled',$prdct[0]],['max_loan_amount','product_json','product_name','id']);
       $prdctjson = json_decode($prdctquery->product_json,true);
       $product = $prdctjson[$prdct[1]];
       $product['amount'] = $prdctquery->max_loan_amount;
       $product['product_name'] = $prdctquery->product_name;
       $product['product_id'] = $prdctquery->id;

       return $product;
   }

     public function is_diff_products($id1, $id2){
        if($id1 == $id2){
            return false;
        }
            
         $product_properties = ['max_loan_amount','flow_fee', 'duration'];
         $products = $this->get_records_by_in('id', [$id1, $id2], $product_properties);
         foreach($product_properties as $prop){
            if($products[0]->$prop != $products[1]->$prop){
               return true;
            }
         }
         return false;

     }
 }
