<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduleColumnsInFieldVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_visits', function (Blueprint $table) {
           
                $table->string('sch_slot',16)->after("visit_purpose")->nullable();
                $table->date('sch_date')->after("sch_slot")->nullable();
                $table->string('sch_status',16)->after("sch_date")->nullable();
                $table->json('sch_purpose')->after("sch_status")->nullable();
    
        });
    }
  
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        
    }
}
