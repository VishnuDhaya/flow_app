<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAccountsCorrectWrongAltAccNums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '781719402' WHERE cust_id='UFLW-759145959' AND acc_number='022127'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '781349048' WHERE cust_id='UFLW-781349048' AND acc_number='029058'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '781350610' WHERE cust_id='UFLW-780344458' AND acc_number='033128'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '774846165' WHERE cust_id='UFLW-701290642' AND acc_number='036172'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '774848452' WHERE cust_id='UFLW-787273551' AND acc_number='037318'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '774852476' WHERE cust_id='UFLW-754477390' AND acc_number='037939'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '774853150' WHERE cust_id='UFLW-774097837' AND acc_number='039273'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772452195' WHERE cust_id='UFLW-773537009' AND acc_number='041076'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772580791' WHERE cust_id='UFLW-772580791' AND acc_number='042317'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '775023690' WHERE cust_id='UFLW-754585104' AND acc_number='044351'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '781722807' WHERE cust_id='UFLW-702472060' AND acc_number='045936'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772102653' WHERE cust_id='UFLW-781485869' AND acc_number='050198'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772105365' WHERE cust_id='UFLW-703889273' AND acc_number='051531'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772202361' WHERE cust_id='UFLW-759930201' AND acc_number='055179'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772118126' WHERE cust_id='UFLW-774976200' AND acc_number='056028'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772109549' WHERE cust_id='UFLW-752862600' AND acc_number='056446'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772203296' WHERE cust_id='UFLW-782827370' AND acc_number='062397'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772119723' WHERE cust_id='UFLW-701902747' AND acc_number='063771'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772116608' WHERE cust_id='UFLW-773069113' AND acc_number='064914'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '772112179' WHERE cust_id='UFLW-785011360' AND acc_number='067141'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789767114' WHERE cust_id='UFLW-787308767' AND acc_number='070080'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789773080' WHERE cust_id='UFLW-759129296' AND acc_number='073882'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789797064' WHERE cust_id='UFLW-704867672' AND acc_number='075909'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789792860' WHERE cust_id='UFLW-783160882' AND acc_number='076123'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789793883' WHERE cust_id='UFLW-753829194' AND acc_number='076704'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789798946' WHERE cust_id='UFLW-772533682' AND acc_number='077451'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789799606' WHERE cust_id='UFLW-701416164' AND acc_number='077773'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789799762' WHERE cust_id='UFLW-788689911' AND acc_number='077821'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789800275' WHERE cust_id='UFLW-778029844' AND acc_number='077974'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789806494' WHERE cust_id='UFLW-789806494' AND acc_number='078442'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789853416' WHERE cust_id='UFLW-753751464' AND acc_number='079959'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789765982' WHERE cust_id='UFLW-785086695' AND acc_number='092217'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '787015110' WHERE cust_id='UFLW-759503503' AND acc_number='197049'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789757877' WHERE cust_id='UFLW-759481999' AND acc_number='607276'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '779519182' WHERE cust_id='UFLW-777422846' AND acc_number='621801'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '782519214' WHERE cust_id='UFLW-785076500' AND acc_number='622959'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '760281205' WHERE cust_id='UFLW-701243408' AND acc_number='673881'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '782700620' WHERE cust_id='UFLW-783269653' AND acc_number='761993'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '773690907' WHERE cust_id='UFLW-782198421' AND acc_number='999732'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789815127' WHERE cust_id='UFLW-789815127' AND acc_number='100239'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '785237954' WHERE cust_id='UFLW-706985252' AND acc_number='646273'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '777768558' WHERE cust_id='UFLW-777768558' AND acc_number='562997'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '787158990' WHERE cust_id='UFLW-772797447' AND acc_number='613439'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '786131163' WHERE cust_id='UFLW-705362690' AND acc_number='975613'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '789762595' WHERE cust_id='UFLW-709291466' AND acc_number='608625'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '776416026' WHERE cust_id='UFLW-777606060' AND acc_number='663866'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '780248071' WHERE cust_id='UFLW-778911915' AND acc_number='776937'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '779756132' WHERE cust_id='UFLW-772251132' AND acc_number='631735'");

        DB::UPDATE("UPDATE accounts SET alt_acc_num = '787064575' WHERE cust_id='UFLW-754502898' AND acc_number='128229'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '786473461' WHERE cust_id='UFLW-786473461' AND acc_number='610346'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '778765481' WHERE cust_id='UFLW-778765481' AND acc_number='621738'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '780274015' WHERE cust_id='UFLW-780274015' AND acc_number='713092'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '778451464' WHERE cust_id='UFLW-771577887' AND acc_number='739489'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '773193623' WHERE cust_id='UFLW-703906940' AND acc_number='758013'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '777865221' WHERE cust_id='UFLW-751980203' AND acc_number='760392'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '785503622' WHERE cust_id='UFLW-753539680' AND acc_number='790736'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '780707150' WHERE cust_id='UFLW-789401017' AND acc_number='807401'");
        DB::UPDATE("UPDATE accounts SET alt_acc_num = '778011173' WHERE cust_id='UFLW-778011173' AND acc_number='810806'");
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
