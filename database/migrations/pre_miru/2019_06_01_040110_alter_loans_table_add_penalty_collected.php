<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoansTableAddPenaltyCollected extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('penalty_collected', 15, 2)->default('0.0')->nullable()->after('paid_amount');
            $table->string('biz_name',80)->nullable()->after('data_prvdr_code');
            $table->string('data_prvdr_cust_id',20)->nullable()->after('biz_name');
               $table->decimal('provisional_penalty', 15, 2)->default('0.0')->nullable()->after('penalty_collected');
        });
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
