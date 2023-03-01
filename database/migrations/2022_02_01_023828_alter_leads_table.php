<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table){
           $table->string('type', 10)->default('kyc')->after('status') ;
           $table->string('kyc_reason', 50)->after('type')->nullable() ;
           $table->string('cust_id', 20)->after('kyc_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table){
           $table->dropColumn('type') ;
           $table->dropColumn('cust_id') ;
           $table->dropColumn('kyc_reason') ;
        });
    }
}
