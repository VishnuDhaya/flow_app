<?php

namespace App\Console\Commands;

use App\Services\HeartBeatService;
use Illuminate\Console\Command;
use Log;
use App\Services\Schedule\ScheduleService;
use Carbon\Carbon;

class WalletAppHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initiate_app_beat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send FCM message to wallet_app to monitor availability';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $s_serv = new ScheduleService();

        $markets = $s_serv->get_markets_to_schedule();
        foreach($markets as $market){
            set_app_session($market->country_code);
            $scheduled = $this->check_schedule_conditions();
            if($scheduled){
                HeartBeatService::beat();
            }

        }
    }

    public function check_schedule_conditions(){
        $now = now();
        $start = Carbon::create($now->year, $now->month, $now->day, 8, 0, 0); 
        $end = Carbon::create($now->year, $now->month, $now->day, 23, 0, 0); 
        if(!$now->between($start, $end, true)){
            return false;
        }
        if($now->dayOfWeek == 0){
            return false;
        }

        return true;
    }
    
}
