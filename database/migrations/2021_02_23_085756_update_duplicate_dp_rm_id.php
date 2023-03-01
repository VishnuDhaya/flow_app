<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDuplicateDpRmId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update borrowers set dp_rel_mgr_id='1244' where dp_rel_mgr_id='1243'");
        DB::update("update borrowers set dp_rel_mgr_id='1241' where dp_rel_mgr_id='1239'");

        DB::update("update loans set dp_rel_mgr_id='1244' where dp_rel_mgr_id='1243'");
        DB::update("update loans set dp_rel_mgr_id='1241' where dp_rel_mgr_id='1239'");

        DB::update("update loan_applications set dp_rel_mgr_id='1244' where dp_rel_mgr_id='1243'");
        DB::update("update loan_applications set dp_rel_mgr_id='1241' where dp_rel_mgr_id='1239'");

        DB::update("delete from persons where id in ('1243','1239')");
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
