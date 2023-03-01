<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnCheckinDistanceInFieldVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->unsignedInteger('checkin_distance')->after('force_checkin_reason')->nullable();
            $table->boolean('early_checkout')->after('force_checkout')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_visits', function (Blueprint $table) {
            //
        });
    }
}
