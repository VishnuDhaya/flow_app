@php
namespace App\Services;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Log;
use App\Consts;

$hidden_fields = false;

if (empty($lead)) {
    $hidden_fields = true;
    $lead = [];
    $lead['biz_name'] = str_repeat('.', 40);
    $lead['cust_name'] = str_repeat('.', 40);
    $lead['cust_mobile_num'] = str_repeat('.', 40);
    $lead['curr_date'] = str_repeat('.', 40);
    $lead = (object) $lead;
}
$lead = (object) $lead;

@endphp


<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv=Content-Type content="text/html; charset=utf-8">
      <style>
         p.MsoNormal, li.MsoNormal, div.MsoNormal, p.MsoNormal1
         {margin-top:0in;
         margin-right:0in;
         margin-bottom:10.0pt;
         margin-left:0in;
         text-align:justify;
         line-height:100%;
         font-size:11.0pt;
         font-family:'Montserrat';}
         p.MsoTitle, li.MsoTitle, div.MsoTitle
         {margin-top:2.0pt;
         margin-right:0in;
         margin-bottom:0in;
         margin-left:0in;
         text-align:center;
         line-height:100%;
         page-break-after:avoid;
         font-size:18.0pt;
         font-family:'Montserrat',;
         font-weight:bold;}
         #footer { position: fixed; left: 0px; bottom: -120px; right: 0px; height: 150px;margin-top:45px;}
         .flow {width:20%;position:absolute; left:50%; top:-20px; transform:translate(-50%)}
         @page WordSection1
         {size:595.3pt 841.9pt;
         margin:.8in 43.15pt 1.0in 43.15pt;}
         div.WordSection1
         { max-width : 90%; margin: auto; }
         .sign{
         max-width: 100px;
         max-height: 50px;
         }
      </style>
   </head>
   <body lang=EN-US style='word-wrap:break-word;'>
      <div style='position:relative'>
         <img src="logo_consent.png"  class="flow" />
      </div>
      <div class=WordSection1>
         <p class=MsoTitle style='margin-bottom:.2in;margin-top:.4in;line-height:normal;'><a name="_p5w1dgewtqn0"></a><span
            lang=EN-GB>INYANDIKO YEMEZA ITANGWA RY’AMAKURU</span></p>
         <p class=MsoNormal style='margin-bottom:8.0pt'><span lang=EN-GB style='font-size:10.0pt;line-height:140%'>Iyi nyandiko isobanura ibyemezwa n’umukiriya itanzwe na <b>{{$lead->cust_name}}</b> kuri <b>FLOW Rwanda Limited (FLOW)</b>.
         FLOW yemewe na Banki nkuru y’u Rwanda hakurikijwe <b>ITEGEKO No 2100 /2018 - 00011[614] Ryo kuwa 12/12/2018</b> RYA
         BANKI NKURU Y’U RWANDA, RIGENGA IBIGO BY’IMARI BIDASHOBORA KUBITSWAMO.
            </span>
         </p>
         <p class=MsoNormal style='margin-bottom:.1in'><b><span lang=EN-GB>AMAKURU Y’UMUKIRIYA</span></b></p>
         <table class=3 border=1 cellspacing=0 cellpadding=0 width=100% style='border-collapse:
            collapse;border:none'>
            <tr>
               <td width=170 valign=top style='width:90%;border:solid black 1.0pt;
                  padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'><b>Izina ry’ubucuruzi</b></span></p>
               </td>
               <td width=170 colspan=3 valign=top style='width:90%;border:solid black 1.0pt;
                  border-left:none;padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'>{{$lead->biz_name}}</span></p>
               </td>
            </tr>
            <tr>
               <td width=170 valign=top style='width:90%;border:solid black 1.0pt;
                  border-top:none;padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'><b>Izina ry’umukiriya</b></span></p>
               </td>
               <td width=170 valign=top style='width:90%;border-top:none;border-left:
                  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
                  padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'>{{ $lead->cust_name }}</span></p>
               </td>
               <td width=170 valign=top style='width:90%;border:solid black 1.0pt;
                  border-left:none;padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'><b>Nimero ya telephone</b></span>
                  </p>
               </td>
               <td width=170 valign=top style='width:90%;border:solid black 1.0pt;
                  border-left:none;padding:6.0pt'>
                  <p class=MsoNormal align=left style='margin-bottom:0in;text-align:left;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'>{{ $lead->cust_mobile_num }}</span></p>
               </td>
            </tr>
         </table>
         <br/>
         <p class=MsoNormal style='margin-bottom:.1in ;margin-top:.1in'><b><span lang=EN-GB>INTEGO</span></b></p>
         <p class=MsoNormal style='margin-bottom:6.0pt'><span lang=EN-GB style='font-size:10.0pt;line-height:140%'>FLOW ikoresha amakuru y’ubucuruzi yo kwinjiza no gusora amafaranga kugira ngo imenye abakiriya bemerewe ibikorwa bya
FLOW, kimwe no guteza imbere no gukomeza kuzamura itangwa ry’ ibicuruzwa byayo. Byongeye kandi, FLOW isaba amakuru
k’ umwirondoro w’umukiriya (Menya umukiriya wawe “KYC”) kugira ngo igenzure neza umwirondoro w’umukiriya.  
            </span>
         </p>
         <p class=MsoNormal style='margin-bottom:.1in;margin-top:.2in'><b><span lang=EN-GB>IBYEMEZWA N’UMUKIRIYA HAMWE N’UMUKONO</span></b></p>
         <p class=MsoNormal style='margin-bottom:6.0pt'><span lang=EN-GB style='font-size:10.0pt;line-height:140%'>Mu gushyira umukono kuri iyi nyandiko, Nge, <b>{{ $lead->cust_name }}</b>: </span>
         </p>
         <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
            margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:15px;
            line-height:120%'><b>1.</b> 
            </span><span lang=EN-GB style='font-size:10.0pt;line-height:140%;padding:0 5px;'>Nemereye FLOW kubona no kubika umwirondoro wange, (Menya umukiriya wawe “KYC”amakuru) hamwe
n’amakuru yo kwinjiza no gusora amafaranga kwange, kugira ngo itange kandi iteze imbere ibicuruzwa byayo. 
            </span>
         </p>
         <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
            margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:15px;
            line-height:120%'><b>2.</b>
            </span><span lang=EN-GB style='font-size:10.0pt;line-height:140%;padding:0 10px;'>Nemereye FLOW gukora isuzuma ry’inguzanyo ikoresheje amakuru yange. 
            </span>
         </p>
         <p class=MsoNormal style='margin-top:.5in'><span lang=EN-GB style='font-size:10.0pt;line-height:115%'> <b>ITALIKI</b>: <b>{{$lead->curr_date}}</b> </span></p>
         <table class=1 border=0 cellspacing=0 cellpadding=0 width=100% style='border-collapse:
            collapse;border:none;margin-top:.8in'>
            <tr>
               <td width=50 valign=top style='width:50.0pt;padding:5.0pt'>
                  <p class=MsoNormal align=center style='margin-bottom:0in;text-align:center;
                     line-height:normal;border:none'>
                     <span lang=EN-GB style='font-size:9.0pt'>
                        @if (isset($cust_sign_file_path) && $cust_sign_file_path)
                        <img src={{ $cust_sign_file_path }} class="sign" /><br />
                        @else
                  <p align=center>.........................</p>
                  @endif
                  </span></p>
               </td>
               <td width=50 valign=top style='width:50.0pt;padding:5.0pt'>
                  <p class=MsoNormal align=center style='margin-bottom:0in;text-align:center;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'>&nbsp;</span></p>
               </td>
            </tr>
         </table>
         <table class=1 border=1 cellspacing=0 cellpadding=0 width=50% style='border-collapse:
            collapse;border:none'>
            <tr>
               <td width=40 valign=top style='width:40.0pt;border:solid white 1.0pt;
                  border-top:solid black 0.1px;padding:5.0pt'>
                  <p class=MsoNormal align=center style='margin-bottom:0in;text-align:center;
                     line-height:normal;border:none'><span lang=EN-GB style='font-size:9.0pt'>Bishyizweho umukono na <b>{{ $lead->cust_name }}</b>
                     (Umukiriya) </span>
                  </p>
               </td>
            </tr>
         </table>
         <div id='footer'>
            <p class=MsoNormal1 align=center style='margin-top:10.0pt;margin-right:0in;
               margin-bottom:0in;margin-left:0in;text-align:center'>
               <span lang=EN-GB
                  style='font-size:7.0pt;line-height:115%;color:grey'>Flow Rwanda Limited ni sosiyete ikorera mu Rwanda | Registration number: 108980952 <br>
                  4th Floor Fairview Building, Westerwelle Startup Haus, Kigali, Rwanda<br>
               </span>
            </p>
         </div>
      </div>
   </body>
</html>