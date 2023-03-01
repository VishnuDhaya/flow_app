<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductGroupColumnMasterAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_agreements', function (Blueprint $table) {
            $table->dropUnique('master_agreements_name_unique');
            $table->string('product_group',32)->after('aggr_type')->default('float_advance');
            $table->unsignedInteger('aggr_duration')->nullable()->change();
            
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
