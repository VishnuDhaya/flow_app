<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_type', 20)->nullable();
            $table->string('cust_id', 20)->nullable();
            $table->string('loan_doc_id', 50)->nullable();            
            $table->unsignedInteger('lead_id')->nullable();
            $table->string('status', 25)->nullable();
            $table->json('approval_json')->nullable();
            $table->json('task_json')->nullable();
            $table->string('remarks', 256)->nullable();
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
        Schema::dropIfExists('new_table_tasks');
    }
}
