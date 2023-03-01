<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnFieldVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->string('cust_id',20)->nullable()->change();
            $table->string('location',32)->nullable()->change();
            $table->string('gps',50)->after('location')->nullable();
            $table->string('cust_gps',50)->after('location')->nullable();
            $table->string('photo_selfie',50)->after('gps')->nullable();
            $table->string('shop_status',10)->after('photo_selfie')->nullable();
            $table->boolean('force_checkout')->after('shop_status')->default(false);
            $table->unsignedInteger('checkout_distance')->after('force_checkout')->nullable();

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
