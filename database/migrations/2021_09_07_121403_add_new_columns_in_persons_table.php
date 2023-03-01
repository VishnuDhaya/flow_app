<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsInPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('relation_with_owner',20)->after('designation')->nullable();
            $table->date('handling_biz_since')->after('relation_with_owner')->nullable();
            $table->string('associated_entity_code',20)->change();
            $table->string('mobile_num_alt_1',20)->after('mobile_num')->nullable();
            $table->string('mobile_num_alt_2',20)->after('mobile_num_alt_1')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            //
        });
    }
}
