<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastDaysToBorrowers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->unsignedInteger('late_3_day_plus_loans')->nullable()->after('tot_default_loans');
            $table->unsignedInteger('late_3_day_loans')->nullable()->after('tot_default_loans');
            $table->unsignedInteger('late_2_day_loans')->nullable()->after('tot_default_loans');
            $table->unsignedInteger('late_1_day_loans')->nullable()->after('tot_default_loans');
            $table->unsignedInteger('late_loans')->nullable()->after('tot_default_loans');
            
            $table->string('category', 20)->nullable()->after('lender_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borrowers', function (Blueprint $table) {
            //
        });
    }
}
