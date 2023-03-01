<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDisbursalDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	$loan_ids = ['UEZM-387471-89448', 'UEZM-613837-43852','UEZM-408791-48958','UEZM-320692-74824', 'UEZM-364651-32797','UEZM-379250-92764', 'UEZM-389171-45118'];
	foreach($loan_ids as $loan_id){
		DB::update("UPDATE loans SET disbursal_date='2019-07-19' WHERE loan_doc_id=?",[$loan_id]);
	}
		DB::update("UPDATE loans SET disbursal_date='2019-05-04' WHERE loan_doc_id='UEZM-924303-29515'");
		DB::update("UPDATE loans SET disbursal_date='2019-07-30' WHERE loan_doc_id='UEZM-436040-97019'");


        DB::update("UPDATE loans SET disbursal_date='2019-09-26' WHERE loan_doc_id='UEZM-191118-58634'");
        DB::update("UPDATE loans SET disbursal_date='2019-09-25' WHERE loan_doc_id='UEZM-925041-41699'");
        DB::update("UPDATE loans SET disbursal_date='2019-09-30' WHERE loan_doc_id='UEZM-772875-85378'");
        DB::update("UPDATE loans SET disbursal_date='2019-10-08' WHERE loan_doc_id='UEZM-133749-96014'");
        DB::update("UPDATE loans SET disbursal_date='2019-10-18' WHERE loan_doc_id='UEZM-603886-24467'");
        DB::update("UPDATE loans SET disbursal_date='2019-10-17' WHERE loan_doc_id='UEZM-154632-61582'");

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
