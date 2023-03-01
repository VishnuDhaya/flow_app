<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('loan_doc_id',50)->nullable();
            $table->string('task_id',40)->nullable();
            $table->string('cmt_type',40)->nullable();
            $table->string('comment',1024)->nullable();
            $table->unsignedInteger('cmt_to')->nullable();
            $table->unsignedInteger('cmt_from')->nullable();  
            $table->string('cmt_to_name',60)->nullable();
            $table->string('cmt_from_name',60)->nullable();
            $table->string('cmt_to_info',255)->nullable();
            $table->string('cmt_from_info',255)->nullable();
            $table->unsignedInteger('created_by')->nullable();   
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_comments');
    }
}
