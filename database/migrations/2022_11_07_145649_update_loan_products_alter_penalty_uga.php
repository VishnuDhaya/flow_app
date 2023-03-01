<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanProductsAlterPenaltyUga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country_code = 'UGA';
        $loan_purpose = 'float_advance';

        $penalty_configs = [
            [
                'max_loan_amount' => 250000, 'duration' => 3, 'fee' => 3500, 'penalty' => 1200
            ],
            [
                'max_loan_amount' => 250000, 'duration' => 6, 'fee' => 6000, 'penalty' => 1000
            ],
            [
                'max_loan_amount' => 500000, 'duration' => 3, 'fee' => 6000, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 500000, 'duration' => 6, 'fee' => 12000, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 750000, 'duration' => 3, 'fee' => 8500, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 750000, 'duration' => 6, 'fee' => 17000, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 1000000, 'duration' => 3, 'fee' => 11000, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 1000000, 'duration' => 6, 'fee' => 22000, 'penalty' => 5000
            ],
            [
                'max_loan_amount' => 1500000, 'duration' => 6, 'fee' => 32000, 'penalty' => 10000
            ],
            [
                'max_loan_amount' => 2000000, 'duration' => 6, 'fee' => 38000, 'penalty' => 10000
            ],
            [
                'max_loan_amount' => 3000000, 'duration' => 6, 'fee' => 55000, 'penalty' => 10000
            ],

        ];

        foreach( $penalty_configs as $penalty_config ) {

            $max_loan_amount = $penalty_config['max_loan_amount'];
            $penalty_amount = $penalty_config['penalty'];
            $fee = $penalty_config['fee'];
            $duration = $penalty_config['duration'];

            DB::SELECT("UPDATE loan_products SET penalty_amount = ? WHERE country_code = ? AND max_loan_amount = ? AND flow_fee = ? AND duration = ? AND loan_purpose = ?", [
                $penalty_amount,    
                $country_code,
                $max_loan_amount,
                $fee,
                $duration,
                $loan_purpose              
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
