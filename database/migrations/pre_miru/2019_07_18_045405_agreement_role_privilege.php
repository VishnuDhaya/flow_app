<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgreementRolePrivilege extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('app_role_privileges')->insert([
            ['role_code' => 'super_admin','priv_code' => 'agreement/create' , 'status' => 'enabled', 'created_by' => 'seed'],
            ['role_code' => 'market_admin','priv_code' => 'agreement/create' , 'status' => 'enabled', 'created_by' => 'seed'], 
            ['role_code' => 'relationship_manager','priv_code' => 'agreement/create' , 'status' => 'enabled', 'created_by' => 'seed'],
            ['role_code' => 'super_admin','priv_code' => 'agreement/list_view' , 'status' => 'enabled', 'created_by' => 'seed'],    
            ['role_code' => 'market_admin','priv_code' => 'agreement/list_view' , 'status' => 'enabled', 'created_by' => 'seed'],               
            ['role_code' => 'relationship_manager','priv_code' => 'agreement/list_view' , 'status' => 'enabled', 'created_by' => 'seed']

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('app_role_privileges')->where([['role_code','super_admin'],['priv_code','agreement/create']])->delete();
        DB::table('app_role_privileges')->where([['role_code','market_admin'],['priv_code','agreement/create']])->delete();
        DB::table('app_role_privileges')->where([['role_code','relationship_manager'],['priv_code','agreement/create']])->delete();
        DB::table('app_role_privileges')->where([['role_code','super_admin'],['priv_code','agreement/list_view']])->delete();
        DB::table('app_role_privileges')->where([['role_code','market_admin'],['priv_code','agreement/list_view']])->delete();
        DB::table('app_role_privileges')->where([['role_code','relationship_manager'],['priv_code','agreement/list_view']])->delete();
    }
}
