<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameLogoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_prvdrs', function (Blueprint $table) {   
            $table->renameColumn('logo','data_prvdr_logo');
        });
        Schema::table('lenders', function (Blueprint $table) {   
            $table->renameColumn('logo','lender_logo');
        });
        Schema::table('acc_providers', function (Blueprint $table) {   
            $table->renameColumn('logo','acc_provider_logo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_prvdrs', function (Blueprint $table) {   
            $table->renameColumn('data_prvdr_logo','logo');
        });
        Schema::table('lenders', function (Blueprint $table) {   
            $table->renameColumn('lender_logo','logo');
        });
        Schema::table('acc_providers', function (Blueprint $table) {   
            $table->renameColumn('acc_provider_logo','logo');
        });
    }
}
