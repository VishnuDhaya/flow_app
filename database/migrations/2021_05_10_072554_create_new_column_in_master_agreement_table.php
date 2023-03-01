<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewColumnInMasterAgreementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_agreements', function (Blueprint $table) {
             $table->unsignedInteger('aggr_duration')->after('valid_upto');
              $table->dateTime('valid_from')->nullable()->change();
              $table->string('product_id_csv',512)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_column_in_master_agreement');
    }
}
