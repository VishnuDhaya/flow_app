<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoanProductsAddProductCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('loan_products', function (Blueprint $table) {
            $table->string('product_code',30)->nullable()->unique()->after('product_name');
        });

        DB::table('loan_products')->update(['product_code' => DB::raw('product_name')]);
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
