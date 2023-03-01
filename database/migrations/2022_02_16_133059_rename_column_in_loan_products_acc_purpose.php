<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnInLoanProductsAccPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_products', function (Blueprint $table) {
            $table->renameColumn('acc_purpose', 'loan_purpose');
        });

        DB::update("update loan_products set loan_purpose = 'float_advance' where loan_purpose != 'terminal_financing'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_products_acc_purpose', function (Blueprint $table) {
            //
        });
    }
}
