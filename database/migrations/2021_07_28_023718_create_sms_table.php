<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('vendor_ref_id')->nullable();
            $table->string('country_code', 5);
            $table->string('mobile_num', 10);
            $table->string('status', 20);
            $table->string('direction', 10);
            $table->string('purpose', 20);
            $table->string('vendor_code', 5);
            $table->string('loan_doc_id', 20)->nullable();
            $table->unsignedInteger('otp_id')->nullable();
            $table->string('content', 350);
            $table->json('callback_json')->nullable();
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
        Schema::dropIfExists('sms_logs');
    }
}
