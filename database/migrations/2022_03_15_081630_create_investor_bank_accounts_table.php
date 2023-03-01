<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('person_id')->unique();
            $table->string('country',15);
            $table->string('currency',3);
            $table->string('first_name',15);
            $table->string('last_name',15);
            $table->string('usd_ach_routing_num',9)->nullable();
            $table->string('usd_account_num',20)->nullable();
            $table->string('eur_bic',11)->nullable();
            $table->string('eur_iban',34)->nullable();
            $table->string('institution',34)->nullable();
            $table->string('address_line_1',64);
            $table->string('address_line_2',64)->nullable();
            $table->string('city',20);
            $table->string('postcode',10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investor_bank_accounts');
    }
}
