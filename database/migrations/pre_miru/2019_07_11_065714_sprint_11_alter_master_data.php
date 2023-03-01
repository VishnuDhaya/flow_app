<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sprint11AlterMasterData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
  $enable = ['enable'];  
  DB::table('master_data')->whereIn('data_code',$enable) ->update(["data_code" => 'enabled', "data_value" => "Enabled" ]);
  $disable = ['disable'];  
  DB::table('master_data')->whereIn('data_code',$disable) ->update(["data_code" => "disabled", "data_value" => "Disabled" ]);
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
