<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFieldVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->boolean('force_checkin')->after('checkout_distance')->default(false);
            $table->string("force_checkin_reason",256)->nullable()->after("force_checkin");  
            $table->renameColumn('photo_selfie','photo_visit_selfie');     
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
