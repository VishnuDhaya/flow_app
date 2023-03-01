@php
namespace App\Services;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Log;
use App\Consts;

$hidden_fields = false;

if (empty($borrower)) {
    $hidden_fields = true;
    $borrower = [];
    $borrower['biz_name'] = str_repeat('.', 40);
    $borrower['biz_type'] = str_repeat('.', 40);
    $borrower['cust_name'] = str_repeat('.', 40);
    $borrower['cust_id'] = str_repeat('.', 40);
    $borrower['cust_mobile_num'] = str_repeat('.', 40);
    $borrower['cust_addr_text'] = str_repeat('.', 100);
    $borrower['acc_prvdr_name'] = str_repeat('.', 40);
    $borrower['acc_type'] = str_repeat('.', 40);
    $borrower['acc_number'] = str_repeat('.', 40);
    $borrower['flow_rel_mgr_name'] = str_repeat('.', 20);
    $borrower['flow_rel_mgr_mobile_num'] = str_repeat('.', 20);
    $borrower['national_id'] = str_repeat('.', 20);
    $borrower['category'] = str_repeat('.', 20);
    $borrower = (object) $borrower;
}
$borrower = (object) $borrower;
if (isset($borrower->acc_prvdr_code)) {
    $cs_num = config('app.customer_success')[$borrower->acc_prvdr_code];
} else {
    $cs_num = str_repeat('.', 20);
}
$witness_mobile_num = $witness_mobile_num ?? "";
@endphp

@extends('agreements.RWA.layout')


@section('title')

    <div class=WordSection1>

        <p class=MsoTitle style='margin-top:0in;line-height:normal;'><a name="_p5w1dgewtqn0"></a><span lang=EN-GB>AMASEZERANO YO GUTANGA INGUZANYO YA FOROTE (FLOAT)</span></p>

        <p class=MsoNormal align=center style='margin-top:6.0pt;text-align:center; line-height:100%'><b><span lang=EN-GB
                    style='font-size:9.0pt;line-height:100%'>{{ $aggr_file_name }}</span></b></p>
@endsection

@section('intro')
        <p class=MsoNormal style='margin-bottom:5.0pt; padding-bottom:2px'>
        <span lang=EN-GB
            style='font-size:9.0pt;line-height:115%'>Aya masezerano akozwe kandi yemejwe na <b>Flow Rwanda Limited (Flow)</b> n’umukiriya <b>{{ $borrower->cust_name }}</b>., Flow yemewe na Banki nkuru y’uRwanda nk’ikigo cy’imari kitabitswamo hakurikijwe itegeko numero 2100 /2018 – 00011[614] ryo  kuwa, 12/12/2018 rya BANKI NKURU Y'URWANDA RIYOBORA IBIGO BY’IMARI BIDASHOBORA KUBITSWAMO BITANGA INGUZANYO GUSA. kubera iyo mpamvu, aya masezerano akozwe mu rwego rwo gushyira mu bikorwa no kubahiriza icyo, itegeko rigenga amasezerano riteganya n’andi mategeko n’amabwiriza y’igihugu cy’uRwanda. Aya masezerano yuzuzwa mw’itumanaho risesuye mbere na nyuma ya buri tangwa ry’inguzanyo ya Forote (float).  </span>
                
        </p>
        <div style="border-top: 0.1px solid; padding: 2px; text-align: center; border-bottom: none;">&nbsp;</div>



@endsection

@section('purpose')

        <p class=MsoNormal style='margin-bottom:0in'><span lang=EN-GB style='font-size:
    6.0pt;line-height:115%'>&nbsp;</span></p>

        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>INTEGO</span></b></p>

        <p class=MsoNormal style='margin-bottom:6.0pt'>
            <span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Intego yo  gutanga inguzanyo ya forote (float) ni uguha intumwa (agent) ubushobozi buhagije  bwo guhaza ibyifuzo by’abakiriya mu bijyanye n’ihererekana ry’amafaranga hakoreshejwe mobile money/murandasi bityo ukagura ubucuruzi bwawe. Gukoresha amafaranga mu buryo bunyuranije n’ubwavuzwe bizafatwa nk’imicungire mibi y’amafaranga no kuyakoresha mu buryo butemewe. </span>
            
        </p>

        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>AMABWIRIZA</span></b></p>

        <p class=MsoNormal style='margin-bottom:5.0pt; padding-bottom : 2px'>
            <span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Igihe cyo kwishyura ntarengwa, ingano y’inguzanyo, hamwe n’inyungu zishyurwa kuri buri nguzanyo bizatangazwa hakoreshejwe ikoranabuhanga. Kunanirwa kwishyura bizagabanya ubushobozi bwawe bwo kubona inguzanyo mu gihe kizaza kandi bifatwa nko kurenga ku mategeko bishobora gutuma ukurikiranwa mu nkiko. </span>
            
        </p>
@endsection

@section('declaration')

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:9.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Nemeye ko nsobanukiwe neza amategeko agenga inguzanyo mpabwa hamwe n’agenga kwishyura</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:9.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Nemeye ko nemeye gufata inguzanyo  ya forote (float)  nabanje kubitekerezaho neza;</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:9.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Nemereye Flow kugenzura no gusesengura amakuru ajyanye n’ubucuruzi bwange hamwe n’itangwa ry’ibicuruzwa</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:9.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Ndemera ko mu gihe naniwe kwishyura ku gihe Flow ishobora gukura amafaranga kuri konti yange; kandi</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:9.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:9.0pt;line-height:115%'>Nemeye ko mu gihe naniwe kwishyura Flow ishobora kundega mu nkiko. </span></p>

    
    <p width="100%" style="float: left">
        <p class=MsoNormal><b>Bikozwe, Kuwa:</b>
        @if (isset($valid_from))
              {{ format_date($valid_from) }}
        @endif
        </p>
    </p>
@endsection
