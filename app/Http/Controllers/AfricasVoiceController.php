<?php

namespace App\Http\Controllers;
use App\Services\Vendors\Voice\AitEventService;
use Log;
use Illuminate\Http\Request;
use App\Services\Vendors\Voice\AitVoiceService;
use App\Services\Vendors\Voice\RMBroadcastService;

class AfricasVoiceController extends Controller
{
   public function process_callback(Request $request){
      $serve = new AitVoiceService;
      $data = $request->all();
      $response = null;
      $data['mobile_num'] = $request->mobile_num;
      
      if($data['destinationNumber'] == config('app.UGA_contact_number')){
         
         if($data['direction'] == 'Inbound'){
            $response = $serve->process_inbound_calls($data);
         }
         else if($data['direction'] == 'Outbound'){
            $response = $serve->handle_active_call($data);
         }

      }else if($data['direction'] == 'Outbound'){

         $response = $serve ->make_calls_outbound($data);
      }
      
      return response($response, 200, ['Content-Type' => 'application/xml']);  
   }

   public function process_event(Request $request){
      
      $event_serve = new AitEventService;
      $voice_serve = new AitVoiceService;
      $data = $request->all();
      $data['mobile_num'] = $request->mobile_num;
      if($data['destinationNumber'] == config('app.UGA_contact_number')){
        
         if($data['direction'] == 'Outbound'){
            $result = $voice_serve->update_call_session($data);
         }elseif($data['direction'] == 'Inbound'){
            $result = $event_serve-> handle_inbound_calls($data);
         }
      }else{
         $result = $event_serve-> handle_inbound_calls($data);
      }
      
      return response($result, 200, ['Content-Type' => 'application/xml']);
   }

}
