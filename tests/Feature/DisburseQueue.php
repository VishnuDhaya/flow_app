<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\WithFaker;
use App\Repositories\SQL\CommonRepositorySQL;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DisburseQueue extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        session()->put('country_code', 'UGA');
        $time_zone = (new CommonRepositorySQL())->get_time_zone('UGA');
        (isset($time_zone)) ? setPHPTimeZone($time_zone) : thrw("Country Undefined");
        $loan_doc_id = 'CCA-135247-62157';
        $srv = new LoanService;
        $srv->send_to_disbursal_queue($loan_doc_id, null);
    }
}