<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableUpgradeFaRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fa_upgrade_requests', function (Blueprint $table) {
            $table->id();
            $table->string('cust_id', 20)->nullable();
            $table->string('type', 15)->nullable();
            $table->string('status', 25)->nullable();
            $table->double('crnt_fa_limit',15,2)->default(0)->nullable();
            $table->double('upgrade_amount',15,2)->default(0)->nullable();
            $table->double('requested_amount',15,2)->default(0)->nullable();
            $table->json('available_amounts')->nullable();
            $table->json('approval_json')->nullable();
            $table->string('acc_prvdr_code',4);
            $table->string('country_code', 3);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fa_upgrade_requests');
    }
}