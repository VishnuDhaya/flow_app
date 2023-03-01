<?php

namespace App\Http\Controllers\Partners\CCA;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\Partners\CCA\ChapChapService;
use Log;

class ChapChapController extends ApiController
{
    public function __construct(Request $req)
    {
        session()->put("country_code", "UGA");
        setPHPTimeZone("EAT");
        $this->serv = new ChapChapService();
    }

    public function req_cust_statement(Request $req) {

        $data = $req->data;
        $resp = $this->serv->req_cust_statement($data);
        return $resp;
    }

    public function req_mul_cust_statement(Request $req) {

        $data = $req->data;
        $resp = $this->serv->req_mul_cust_statement($data);
        return $resp;
    }

    public function statement_req_callback(Request $req) {

        $data = $req->data;
        $resp = $this->serv->statement_req_callback($data);
        return $resp;
    }
}
