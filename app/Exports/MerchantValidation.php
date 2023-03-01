<?php

namespace App\Exports;

use App\Scripts\php\CustNameScript;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class MerchantValidation implements FromView, ShouldAutoSize
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

        return view('exports.customer',[
            'users' => CustNameScript::getCustName()
        ]); 
    }

}
