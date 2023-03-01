<?php

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCustCsfValuesDisabledAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_app_session('UGA');
        $acc_prvdr_code = 'UMTN';
        $delete_before = Carbon::now()->subMonth()->toDateString();

        $account_repo = new Account;
        $accounts = $account_repo->get_records_by_many(['acc_prvdr_code', 'country_code', 'status'], [$acc_prvdr_code, 'UGA', 'disabled']);

        if(!empty($accounts)) {
            $acc_numbers = csv((collect($accounts)->pluck('acc_number')->unique()->toArray()));
            DB::DELETE("DELETE FROM cust_csf_values WHERE acc_prvdr_code = ? AND acc_number in ($acc_numbers)", [$acc_prvdr_code]);
        }

        DB::DELETE("DELETE FROM cust_csf_values WHERE acc_prvdr_code = ? AND acc_number NOT IN (SELECT DISTINCT acc_number FROM accounts WHERE acc_prvdr_code = ? and status = ?) AND (created_at < ? OR created_at IS NULL)", [$acc_prvdr_code, $acc_prvdr_code, 'enabled', $delete_before]);
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
