<?php

use App\Repositories\SQL\LeadRepositorySQL;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FlowApp\AppUser;


class UpdateAuditedByOnExistingLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $borrowers = DB::select("select lead_id,created_by from borrowers where lead_id is not null and created_by is not null");
       foreach($borrowers as $borrower){
            $role_codes = AppUser::where('id',$borrower->created_by)->get('role_codes')->pluck("role_codes")[0];
            if($role_codes == 'operations_auditor'){
                DB::update("update leads set audited_by = {$borrower->created_by} where id = {$borrower->lead_id}");
            }

       }
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
