<?php
namespace App\Services;
use Log;
use App\Consts;
use App\Exceptions\FlowCustomException;
use App\Services\Mobile\RMService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use AWS;

class TextExtractionService{
	
  public function extract_text($file_data,$contents){
    
    $aws_client = config('app.text_extract_client')[session('country_code')];
    if($aws_client == 'textract'){
      $resp = $this->extract_using_textract($file_data,$contents);

    }else if($aws_client == 'reckognition'){
      $resp = $this->extract_using_reckognition($file_data,$contents);
    }
    return $resp;

  }

  private function extract_using_reckognition($file_data,$contents){
    $rekognition_client = AWS::createClient('rekognition');
    $options = [
      "Image"=> ["Bytes"=> $contents,],
       // REQUIRED
    ];
    $text = $rekognition_client->detectText($options);
    $detections = $text['TextDetections'];
    $line_data = [];
    // Loop through all the blocks:
    foreach ($detections as $key => $value) {
      if (isset($value['Type']) && $value['Type']) {
          $Type = $value['Type'];
          if (isset($value['DetectedText']) && $value['DetectedText']) {
              $text = $value['DetectedText'];
              if ($Type == 'LINE') {
                  array_push($line_data, $text);  
              }
          }
      }
    }
     
    $data = [];
  
    if($file_data['country_code'] == 'UGA'){
    
      if ($file_data['file_of'] == "photo_national_id" && check_val_exists($line_data,"NATIONAL") ) {
        $data = $this->process_uga_nationalid($line_data, $file_data);
      }else{
        thrw("Check if you have uploaded a valid {$file_data['file_of']}");
      }
    }
    elseif($file_data['country_code'] == 'RWA'){
      if ($file_data['file_of'] == "photo_national_id" && check_val_exists($line_data,"RWANDA") ) {
          $data = $this->process_rwa_nationalid($line_data, $file_data);
      }else{
        thrw("Check if you have uploaded a valid {$file_data['file_of']}");
      }
    }
    else{
      thrw("Text extraction not implemented for this market/country {$file_data['country_code']}");
    }

    
    return $data;
  
  }

  private function extract_using_textract($file_data,$contents){
    $textract_client = AWS::createClient('textract');
    $options = [
      "Document"=> ["Bytes"=> $contents,],
      'FeatureTypes' => ['FORMS']
    ];

    $text = $textract_client->analyzeDocument($options);
    $blocks = $text['Blocks'];
    $data = [];
    
    foreach ($blocks as $block) {
        if ($block["BlockType"] == "KEY_VALUE_SET" && $block['EntityTypes'][0] == "KEY" ){
          $key_ids = $block['Relationships'][1]['Ids'];
          $value_ids = $block["Relationships"][0]["Ids"][0];
          $key = $this->get_key($key_ids,$blocks);
          $value = $this->get_value($value_ids,$blocks);
          $data[$key] = $value;
        } 
    }

    if($file_data['country_code'] == 'UGA'){
      if ($file_data['file_of'] == "photo_national_id" && array_key_exists("NIN",$data) ) {
        $resp = [];
        $this->validate_exp_date($data,$resp);
        $data['DATE OF BIRTH'] = parse_date($data['DATE OF BIRTH'],"d.m.Y")->format("Y-m-d");
        $key_arr = ['GIVEN NAME' => 'first_name','SURNAME' => 'last_name','DATE OF BIRTH' => 'dob', 'SEX' =>'gender',
                    'DATE OF EXPIRY' => 'national_id_exp_date','NIN' => 'national_id' ,];
        foreach($key_arr as $key => $value){
          $resp[$key_arr[$key]] = $data[$key];
        }
      }else{
        thrw("Check if you have uploaded a valid {$file_data['file_of']}");
      }
    }else{
      thrw("Text extraction not implemented for this market/country {$file_data['country_code']}");
    }
    return $resp ;
  }

  private function process_uga_nationalid($line_data, $file_data){
    Log::warning('line data UGA');
    Log::warning($line_data);


    $data = array();
    $dob_captured = false;
    $pattern= "/^([0-2][0-9]|(3)[0-1])(.)(((0)[1-9])|((1)[0-2]))(.)\d{4}$/";

    try{
        foreach($line_data as $index => $value){
          if($value == "SURNAME"){
            $data['last_name'] = $line_data[$index+1];

          }else if($value == "GIVEN NAME"){
            $given_name= $line_data[$index+1];
            $names = split_names($given_name);
            
            $data['first_name'] = $names['first_name'];
            if(array_key_exists('middle_name' , $names)){
              $data['middle_name'] = $names['middle_name'];
            }
          
          }
//           else if(Str::startsWith($value, 'UGA')){
//               if(strlen($value) == 16){
//                 $split_values = explode(' ', $value);
//                 $data["dob"] = parse_date($split_values[2],"d.m.Y")->format("Y-m-d");
//                 $data["gender"] = $split_values[1];
//               }else if(strlen($value) == 3 && strlen($line_data[$index+1]) == 1 && strlen($line_data[$index+2]) == 10 ){
//                 $data["gender"] = $line_data[$index+1];
//                 $data["dob"] = parse_date($line_data[$index+2],"d.m.Y")->format("Y-m-d");
//               }
              
//           }
          else if($index <= 13 && $dob_captured == false && preg_match($pattern, $value)){
              $data["dob"] = parse_date(trim($value),"d.m.Y")->format("Y-m-d");
              $dob_captured = true;
          }
          else if($value == 'M' || $value == 'F'){
             $data["gender"] = $value;
          }
          else if(Str::startsWith($value, 'NIN')){
              $inner_arr = array_slice($line_data, $index+1);
              foreach($inner_arr as $inner_val){
                $split_values = explode(' ', $inner_val);
                if(sizeof($split_values) <= 2 && strlen($split_values[0]) == 14){
                  $data["national_id"] = $split_values[0];
                  break;
                }
              }
          }else if(Str::startsWith($value, 'DATE') && strlen($value) == 14){
            $expiry_date = parse_date($line_data[$index+1],"d.m.Y")->format("Y-m-d");
            $data['national_id_exp_date'] = $expiry_date;
            $curr_date = Carbon::now()->format("Y-m-d");
            $months_to_exp = Carbon::now()->addMonths(6)->format("Y-m-d");
            if($curr_date > $expiry_date){ 
              thrw("The ID is expired");
            }elseif($expiry_date < $months_to_exp){ 
            
              $data['warning'] = "The ID is expiring within next six months. Please ask the customer to renew the ID";
            }
          }
        }
        return ['processed' => $data, 'raw' => $line_data];
      }
      catch(\Exception $e){
        $err_msg = $e->getMessage();
        $trace = $e->getTraceAsString();
        $nid_data = ['processed' => $data, 'raw' => $line_data];
        (new RMService)->send_nid_notification_mail($file_data, $err_msg, $nid_data, $trace);
      } 
  }

  private function process_rwa_nationalid($line_data, $file_data){
    Log::warning('line data RWA');
    Log::warning($line_data);
    $data = array();
    $pattern = "/^([0-2][0-9]|(3)[0-1]|[1-9])(\/)(((0)[1-9])|((1)[0-2])|[1-9])(\/)\d{4}$/";

    try{
      foreach($line_data as $index => $value){
        if(str_contains($value, 'Names')) {        
          $names = explode(' ', $line_data[$index+1]);
          $names = array_map('strtoupper', $names);
          // $data['first_name'] = $names[0];
          $data['first_name'] = trim($names[0], ',');
          if(sizeof($names) > 1){
            $data['last_name'] = end($names);
          }      
          if (sizeof($names) > 2) {
            $data['middle_name'] = implode(' ', array_slice($names, 1, -1));
          }
        }
        else if(str_contains($value, 'Gore') || str_contains($value, 'Gabo')){
          $gender_data = explode('/', $value);
          $data['gender'] = trim($gender_data[1]);
        }

        else if(preg_match($pattern, $value)){
          $data["dob"] = parse_date(trim($value),"d/m/Y")->format("Y-m-d");
        }

      
        else if(str_contains($value, 'National ID No')){
          if(strlen($value) <= 28){
          
            if(isset($line_data[$index+1])){
              $id_data = $line_data[$index+1];
            }else{
              $id_data = $line_data[$index-1];
            }
          
          }else if(strlen($value) > 28){
            $id_data = $line_data[$index];
          }
          $data["national_id"] = filter_var($id_data, FILTER_SANITIZE_NUMBER_INT);
        }
      }
      return ['processed' => $data, 'raw' => $line_data];

    }
    catch(\Exception $e){
      $err_msg = $e->getMessage();
      $trace = $e->getTraceAsString();
      $nid_data = ['processed' => $data, 'raw' => $line_data];
      
      (new RMService)->send_nid_notification_mail($file_data, $err_msg, $nid_data, $trace);

    }
  }

  private function validate_exp_date(&$data,&$resp){
    if(array_key_exists("DATE OF EXPIRY" ,$data ) && strlen($data['DATE OF EXPIRY']) == 10 ){
      $data['DATE OF EXPIRY'] =  $expiry_date = parse_date($data['DATE OF EXPIRY'],"d.m.Y")->format("Y-m-d") ;
      $curr_date = Carbon::now()->format("Y-m-d");
      $months_to_exp = Carbon::now()->addMonths(6)->format("Y-m-d");
      if($curr_date > $expiry_date){ 
        thrw("The ID is expired");
      }elseif($expiry_date < $months_to_exp){ 
        $resp['warning']  = "The ID is expiring within next six months. Please ask the customer to renew the ID";
      }
    }else{
      thrw("Invalid Date Format");
    }
  }

  private function get_key($ids,$blocks){
    $text = '';
    foreach ($blocks as $block) {
      foreach ($ids as $id){
        if ($block["BlockType"] == "WORD" && $block['Id'] == $id ){
          $text = $text . ' ' . $block['Text'];
          $text = ltrim($text);
        }
      }
    }
    return $text;
  }

  private function get_value($id,$blocks){
    foreach($blocks as $block){
      if ($block["BlockType"] == "KEY_VALUE_SET" && $block["Id"]== $id){
        $value_ids = $block["Relationships"][0]["Ids"];
        $value_text = '';
        foreach ($value_ids as $value_id){
          $value_text = $value_text . ' ' . $this->get_key($value_ids,$blocks);
          $value_text = ltrim($value_text);
        }
      }
    }
    return $value_text;
  }


}
