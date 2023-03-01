<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLoansTableChangeAmtDefaults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('due_amount', 15, 2)->default('0.0')->change();
            $table->decimal('current_os_amount', 15, 2)->default('0.0')->change();
            $table->decimal('paid_amount', 15, 2)->default('0.0')->change();
            $table->decimal('waiver_amount', 15, 2)->default('0.0')->change();
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
