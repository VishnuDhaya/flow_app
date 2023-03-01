<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataKeysAddDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_data_keys', function (Blueprint $table) {
            $table->dropColumn('key_group');
            $table->string('data_type',16)->nullable(false)->after('parent_data_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_data_keys', function (Blueprint $table) {
            $table->string('key_group',60)->nullable()->after('key_desc');
            $table->dropColumn('data_type');
        });
    }
}
