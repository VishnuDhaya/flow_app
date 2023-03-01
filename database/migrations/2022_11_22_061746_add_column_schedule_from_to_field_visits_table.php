<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class AddColumnScheduleFromToFieldVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{

            DB::beginTransaction();
            
            //Alter table column
            Schema::table('field_visits', function (Blueprint $table) {
                $table->string("sch_from", 20)->after("sch_remarks");
            });
            
            //Call Seeder
            $seed = (new \Database\Seeders\ScheduleFromSeeder());
            $seed();
            DB::commit();

        }catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_visits', function (Blueprint $table) {
                $table->dropColumn('sch_from');
        });
    }
}
