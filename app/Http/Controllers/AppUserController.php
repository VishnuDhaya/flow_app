<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SQL\AppUserRepositorySQL;
use Illuminate\Support\Facades\Validator;
use App\Validators\FlowValidator;

class AppUserController extends ApiController
{


    public function list(Request $req)
    {
        $data = $req->data;

        
        $app_user_repo = new AppUserRepositorySQL();

        $approver_detail = $app_user_repo->get_user_by($data["country_code"],$data["role_codes"]);


        return $this->respondData($approver_detail);
    }



}
