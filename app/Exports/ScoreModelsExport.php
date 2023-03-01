<?php

namespace App\Exports;

use App\Scripts\php\ScoreEligibilityScript;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ScoreModelsExport implements FromView, ShouldAutoSize
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

        $score_type = 'by_models';
        if($score_type == 'by_models'){
            return view('exports.view',[
                'users' => ScoreEligibilityScript::calcScoreModel($score_type)
            ]);
        }
        else if($score_type == 'by_products') {
            
        } 
    }

}
