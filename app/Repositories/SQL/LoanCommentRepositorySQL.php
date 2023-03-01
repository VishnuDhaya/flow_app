<?php
namespace App\Repositories\SQL;

use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\SQL\LoanCommentRepositorySQL;
use App\Repositories\SQL\PersonRepositorySQL;
use App\Repositories\SQL\MasterDataRepositorySQL;
use App\Models\LoanComment;
use App\Services\Support\SMSNotificationService;
use App\Services\Support\SMSService;
use App\Services\Schedule\EmailScheduleService;
USE App\Mail\EmailComments;
use App\SMSTemplate;
use Mail;

use Log;

class LoanCommentRepositorySQL  extends BaseRepositorySQL implements BaseRepositoryInterface
{
	public function __construct()
  	{
      parent::__construct();
    }
    public function model(){
			return LoanComment::class;
	}
	public function create(array $loan_comment){
		
        $result = ["sms" => false];
        $result = ["email" => false];
        $person_repo = new PersonRepositorySQL();
		$loan_comment['cmt_from'] = session('user_id');
		$user_id = session('user_id');
		if(isset($loan_comment['cmt_to']))
		{
			$person_id  = $loan_comment['cmt_to'];
		    $assign_detail = $person_repo->get_person_contacts($person_id); 
			$loan_comment['cmt_to_info'] = $assign_detail->email_id.", ".$assign_detail->mobile_num;
			$cmt_to_name = $person_repo->full_name($person_id);
			$loan_comment['cmt_to_name'] = $cmt_to_name;
		}
		$comment = $person_repo->get_person_id($user_id);
		$comment_id = $comment[0]->person_id;
		$comment_detail = $person_repo->get_person_contacts($comment_id); 
		$loan_comment['cmt_from_info'] = $comment_detail->email_id.", ".$comment_detail->mobile_num;  
		$cmt_from_name = $person_repo->full_name($comment_id);
        $loan_comment['cmt_from_name'] = $cmt_from_name;
		$loan_comment_id = parent::insert_model($loan_comment);
		$cmt_type = $loan_comment['cmt_type'];
		$master_repo = new MasterDataRepositorySQL();
        unset($loan_comment['cmt_from']);
        unset($loan_comment['cmt_type']);

        //$data_value = $master_repo->get_master_data($data_code);
        $data_value = data_value($cmt_type);
        $loan_comment['cmt_type'] = $data_value;
        $market_repo = new MarketRepositorySQL();
        $isd_code = $market_repo->get_isd_code($loan_comment['country_code']);
		$msg = compile_sms(SMSTemplate::LOAN_COMMENT_MSG, $loan_comment);
		if(isset($loan_comment['sms']) && ($loan_comment['sms']))
		{
			$sms_serv = new SMSService();
			$result["sms"] = $sms_serv($assign_detail->mobile_num, $msg, $isd_code->isd_code);
		}
		if(isset($loan_comment['email']) && ($loan_comment['email']))
		{
			
		   Mail::to($assign_detail->email_id)->send(new EmailComments($loan_comment));
        }
        return $result;
 	}



	public function update(array $id){
		throw new BadMethodCallException();
	}

	public function delete($id){
		throw new BadMethodCallException();
	}

	public function list($loan_doc_id)
    {
        return parent::get_records_by('loan_doc_id',$loan_doc_id,['loan_doc_id','cmt_type','comment','cmt_to_name','cmt_from_name', 'cmt_from' , 'cmt_to' , 'cmt_to_info','cmt_from_info', 'created_at'],null);
    }
}
