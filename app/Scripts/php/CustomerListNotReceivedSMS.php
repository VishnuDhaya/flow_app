<?php



namespace App\Scripts\php;

use App\Services\Support\SMSService;

class CustomerListNotReceivedSMS{

    public function send_draft_sms(){

        $sms_serv = new SMSService();
        $isd_code = '256';
        $cust_mobile_nums = ['774876689','779588376','787996045','788310520','701549442','752164862','757953801','761555077','770654888','770711808','770801386','771879597','774780313','775553678','776229811','776739273','777033412','777459827','778012557','778702361','778956544','779684900','780553555','781462259','782356633','782827362','783626712','783810497','784025736','784515937','394002199','700964116','700970426','701217095','701219030','701434555','708661439','704067680','704851975','705122666','705505060','706332171','750674037','751122901','751524755','751943061','760015546','752087040','752479693','752668101','704183540','753878718','755108544','755369240','755428641','756991178','758208060','700937388','758755900','758830875','759406298','759486428','759748264','760094131','760448049','761425954','762106854','762305440','762305740','762872185','770333067','770813515','770942402','771910953','772366934','772706094','772775282','772777197','773322664','773384410','773625383','773629021','778608862','774061298','774092302','774131839','774348945','774459335','774706224','774887450','774956241','775176973','775256906','775667192','776215695','776723376','776874154','777022055','777098073','777356621','777395097','777667707','777722668','777770732','777826584','777991901','778069508','778842642','779229921','779293102','779340375','779581071','779917781','762695754','780574266','780584557','786455425','780846328','781455114','781481308','781602746','781663364','781946568','782048178','782326667','782494742','782543757','782733435','782794303','782816816','782845033','782965652','782989830','783292705','783489015','783875276','784031870','784160878','784393880','784412489','771670722','784650867','784691374','784768653','784890653','785374565','785625417','785714275','786225750','786459005','786757193','786826472','786875000','786949504','787756422','787848120','787887963','788249606','788298570','788318744','774345957','788658506','788766485','788854374','788858307','788888339','789305231','789401017','706895128','784124174','392004116','392580526','758370329','700166813','706240464','708083421','751597682','751906478','756329189','770403415','771214363','771611052','771626829','771821078','772308959','772920757','772994597','773706374','773909157','774046003','774431300','775401982','776659000','776700059','776795375','777070717','777171866','777243457','777377515','779581304','780393125','781932050','782206823','782443795','782463005','782598935','783093955','783336034','783401846','784301403','784344313','784607852','785042363','785842774','786197280','787531054','788060222','788115566','788239129','788548118','789178958'];

        $message = "Please pay on time to get float advance without any interruption.";
        foreach($cust_mobile_nums as $mobile_num){
            $sms_serv->send_sms($mobile_num, $message, $isd_code);
        }
    }
    

}



