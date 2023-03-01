<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustCommissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('acc_prvdr_code',4)->nullable();
            $table->string('acc_number',150)->nullable();
            $table->string('alt_acc_num',20)->nullable();
            $table->year('year',4)->nullable();
            $table->json('commissions')->nullable();
            $table ->unsignedInteger('created_by')->nullable();
            $table ->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->index('alt_acc_num');
        });
        DB::statement("ALTER TABLE cust_commissions MODIFY column commissions JSON DEFAULT (JSON_OBJECT())");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cust_commissions');
    }
}
