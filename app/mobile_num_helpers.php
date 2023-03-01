<?php
use Illuminate\Support\Str;
use \libphonenumber\PhoneNumberUtil;




function split_mobile_num($mobile_num){
        $mobile_num = ltrim($mobile_num,'+');
        $mobile_num = '+'.$mobile_num;
        $num_util = PhoneNumberUtil::getInstance();
        
        try {
            $num_obj = $num_util->parse($mobile_num);
           
            return [$num_obj->getNationalNumber(), $num_obj->getCountryCode()];
        } 
        catch (\libphonenumber\NumberParseException $e) {
            thrw($e);
            }
        }
    
function get_phone_num($payer, $isd_code){

	        $isd_code = "+". $isd_code;
	        $mob_num = null;
	        if(Str::startsWith($payer, $isd_code)){
	
	            $mob_num = str_replace($isd_code, '',$payer);
	        }
	        return $mob_num;
	    }
    
function clean_mobnums($isd_code, $recipients){
		$recipients = explode(',', $recipients);
		//Log:warning($recipients);
		foreach($recipients as &$recipient) {
		  $recipient = clean_mobnum($recipient);
		  $recipient = $isd_code.$recipient;
		 
		}
		return implode(",", $recipients);
	}
	
function clean_mobnum($recipient){
		
		if(Str::startsWith($recipient, '0')){
		  $recipient = ltrim($recipient, '0'); 
		}
		return $recipient;
	}