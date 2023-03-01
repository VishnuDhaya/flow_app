<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsBorrowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function (Blueprint $table){
            $table->string('guarantor1_name',50)->after('photo_biz_lic')->nullable();
            $table->string('guarantor2_name',50)->after('guarantor1_name')->nullable();
            $table->string('lc_name',50)->after('guarantor2_name')->nullable();
            $table->string('guarantor1_doc',50)->after('lc_name')->nullable();
            $table->string('guarantor2_doc',50)->after('guarantor1_doc')->nullable();
            $table->string('lc_doc',50)->after('guarantor2_doc')->nullable();

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
