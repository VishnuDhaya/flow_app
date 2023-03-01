<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductGroupToAccPurpose extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('product_group', 'acc_purpose');
        });
        Schema::table('cust_agreements', function (Blueprint $table) {
            $table->renameColumn('product_group', 'acc_purpose');
        });
        Schema::table('master_agreements', function (Blueprint $table) {
            $table->renameColumn('product_group', 'acc_purpose');
        });
        Schema::table('loan_products', function (Blueprint $table) {
            $table->renameColumn('product_group', 'acc_purpose');
        });
        Schema::table('borrowers', function (Blueprint $table) {
//            $table->dropColumn('product_group');
            $table->renameColumn('product_group', 'acc_purpose');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('acc_purpose', 'product_group');
        });
        Schema::table('cust_agreements', function (Blueprint $table) {
            $table->renameColumn('acc_purpose', 'product_group');
        });
        Schema::table('master_agreements', function (Blueprint $table) {
            $table->renameColumn('acc_purpose', 'product_group');
        });
        Schema::table('loan_products', function (Blueprint $table) {
            $table->renameColumn('acc_purpose', 'product_group');
        });
        Schema::table('borrowers', function (Blueprint $table) {
//            $table->string('product_group', 20)->nullable();
            $table->renameColumn('acc_purpose', 'product_group');


        });
    }
}
