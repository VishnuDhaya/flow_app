<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateCustIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db = config('database.connections.mysql.database');
        $mobile_nums = collect(DB::select("select mobile_num from borrowers b, persons p where p.id = b.owner_person_id group by mobile_num having count(*) > 1"))->pluck('mobile_num');
        $mobile_nums = csv($mobile_nums);
        $tables = DB::select("SELECT TABLE_NAME AS  'table_name' from INFORMATION_SCHEMA.COLUMNS where COLUMN_NAME = 'cust_id' and TABLE_SCHEMA = '$db' and TABLE_NAME not in ('borrowers', 'cash_backs')");
        foreach($tables as $table){
            DB::update("update {$table->table_name} x, borrowers b, persons p set x.cust_id = CONCAT('UFLW-', p.mobile_num) where x.cust_id = b.cust_id and b.owner_person_id = p.id and p.mobile_num not in ($mobile_nums)");
        }
        DB::statement("alter table borrowers add column old_cust_id varchar(20) null after cust_id");
        DB::update("update borrowers set old_cust_id = cust_id");
        DB::update("update borrowers b, persons p set b.cust_id = CONCAT('UFLW-', p.mobile_num) where b.owner_person_id = p.id  and  p.mobile_num not in ($mobile_nums)");
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
