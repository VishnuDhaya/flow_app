<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecordAuditsColoumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('field_visits', function (Blueprint $table) {
            DB::update("update field_visits set visit_purpose = concat('[\"',visit_purpose,'\"]')"); 
            DB::statement("ALTER TABLE field_visits CHANGE visit_purpose visit_purpose JSON  DEFAULT NULL");
            #$table->json('visit_purpose')->change()->charset('');
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
