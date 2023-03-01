<?php

use App\Consts;
use App\Models\FlowApp\AppUser;
use App\Services\LeadService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeadsMoveRemarksToJsonColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::UPDATE("UPDATE leads SET new_remarks = '[]'");
        DB::STATEMENT("ALTER TABLE leads MODIFY COLUMN new_remarks JSON DEFAULT (JSON_ARRAY())");

        $leads_without_audit_id = DB::SELECT("SELECT id, updated_by, country_code FROM leads WHERE remarks IS NOT NULL and audited_by IS NULL");
        $audited_by_based_on_country = ['UGA' => 33, 'RWA' => 52];
        foreach ( $leads_without_audit_id as $lead_without_audit_id ) {
            $role_code = AppUser::where('id',$lead_without_audit_id->updated_by)->get('role_codes')->pluck("role_codes")[0];
            if($role_code == 'operations_auditor'){
                $user_id = $lead_without_audit_id->updated_by;
            }
            else {
                $user_id = $audited_by_based_on_country[$lead_without_audit_id->country_code];
            }

            $audited_by = DB::SELECT("SELECT person_id FROM app_users WHERE id = ?", [$user_id])[0]->person_id;
            DB::UPDATE("UPDATE leads SET audited_by = ? WHERE id = ?", [$audited_by, $lead_without_audit_id->id]);
        }

        $existing_remarks = DB::SELECT("SELECT id, country_code, audited_by, updated_at, remarks, reassign_reason, close_reason FROM leads WHERE remarks is NOT NULL");
        
        foreach($existing_remarks as $existing_remark) {
            
            set_app_session($existing_remark->country_code);
            $action = (isset($existing_remark->reassign_reason)) ? Consts::LEAD_ACTIONS[Consts::LA_KYC_REASSIGNED] : Consts::LEAD_ACTIONS[Consts::LA_KYC_REJECTED];

            $remark_details = (new LeadService)->combine_remarks([], $existing_remark->remarks, $existing_remark->audited_by);
            $remark_details = (new LeadService)->combine_remarks($remark_details, null, $existing_remark->audited_by, $action);
            $remark_details = json_encode($remark_details);
            DB::UPDATE("UPDATE leads SET new_remarks = ? WHERE id = ?", [$remark_details, $existing_remark->id]);
        }

        DB::STATEMENT("ALTER TABLE leads RENAME COLUMN remarks TO old_remarks");
        DB::STATEMENT("ALTER TABLE leads RENAME COLUMN new_remarks TO remarks");

        DB::STATEMENT("ALTER TABLE leads DROP COLUMN old_remarks");
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
