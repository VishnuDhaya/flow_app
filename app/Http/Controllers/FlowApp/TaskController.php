<?php

namespace App\Http\Controllers\FlowApp;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\TaskService;

use Log;
use Illuminate\Support\Facades\Session;

class TaskController extends ApiController
{

    public function create_task(Request $request){
        $data = $request->data;
        $task_serv = new TaskService();
        $result = $task_serv->create_task($data);
       
        if($result['task_id']){
            return $this->respondSuccess($result['message']);  
        }else{
            return $this->respondWithError("Unable to send {$data['task_type']}");
        }  
        return $result;
   
    }

    public function list_tasks(Request $request){
        $data = $request->data;
        $task_serv = new TaskService();
        $result = $task_serv->list_tasks($data);
       
        return $this->respondData($result);    
    }

    public function task_approval(Request $request){
        $data = $request->data;
        $task_serv = new TaskService();
        $result = $task_serv->task_approval($data);
       
        if($result){
            return $this->respondSuccess(dd_value($data['task_type'])." {$result['status']} successfully");  
        }else{
            return $this->respondWithError("unable to {$result['status']} ".dd_value($data['task_type']));
        }    
    }

}
