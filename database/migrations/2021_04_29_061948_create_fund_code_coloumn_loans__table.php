<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundCodeColoumnLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('loans', function (Blueprint $table) {
            $table->string('fund_code',15)->default('flow_int')->after('lender_code');
            $table->double('paid_principal' ,15,2)->nullable()->after('paid_by');
            $table->double('paid_fee' ,15,2)->nullable()->after('paid_by');
            $table->double('paid_excess' ,15,2)->nullable()->after('paid_by');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_code_coloumn_loans_');
    }
}
