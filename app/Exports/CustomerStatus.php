<?php

namespace App\Exports;

use App\Scripts\php\CustStatusScript;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerStatus implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     $score_type = 'by_models';
    //     return CalcScoreModel::calcScoreModel($score_type);
        
    // }

    public function view(): View {

        return view('exports.cust_status',[
            'users' => CustStatusScript::getCustStatus()
        ]);
    }

}
