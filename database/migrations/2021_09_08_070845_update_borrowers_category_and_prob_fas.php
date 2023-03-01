<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBorrowersCategoryAndProbFas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update probation_period set status = 'completed' where id =1090");
        DB::update("update borrowers set category='Condonation' where id = 1049");
        DB::update("update borrowers set category='Condonation' where id = 1015");
        DB::update("update borrowers set category='Condonation' where id = 1073");
        DB::update("update borrowers set category='Condonation' where id = 1544");
        DB::update("update borrowers set category='Condonation' where id = 1405");
        DB::update("update borrowers set category='Condonation' where id = 1399");
        DB::update("update borrowers set category='Condonation' where id = 1608");
        DB::update("update borrowers set category='Condonation' where id = 1612");
        DB::update("update borrowers set category='Condonation' where id = 1654");
        DB::update("update borrowers set category='Condonation' where id = 1495");
        DB::update("update borrowers set category='Condonation' where id = 1825");
        DB::update("update borrowers set category='Condonation' where id = 1606");
        DB::update("update borrowers set category='Condonation' where id = 1685");
        DB::update("update borrowers set category='Condonation' where id = 1720");
        DB::update("update borrowers set category='Condonation' where id = 1542");
        DB::update("update borrowers set category='Condonation' where id = 2131");
        DB::update("update borrowers set category='Condonation' where id = 1837");
        DB::update("update borrowers set category='Condonation' where id = 1512");
        DB::update("update borrowers set category='Condonation' where id = 1626");
        DB::update("update borrowers set category='Condonation' where id = 1818");
        DB::update("update borrowers set category='Condonation' where id = 1470");
        DB::update("update borrowers set category='Condonation' where id = 1498");
        DB::update("update borrowers set category='Condonation' where id = 1884");
        DB::update("update borrowers set category='Condonation' where id = 1459");
        DB::update("update borrowers set category='Condonation' where id = 1855");
        DB::update("update borrowers set category='Condonation' where id = 1388");
        DB::update("update borrowers set category='Condonation' where id = 1970");
        DB::update("update borrowers set category='Condonation' where id = 1879");
        DB::update("update borrowers set category='Condonation' where id = 1497");
        DB::update("update borrowers set category='Condonation' where id = 1653");
        DB::update("update borrowers set category='Condonation' where id = 1408");
        DB::update("update borrowers set category='Condonation' where id = 1427");
        DB::update("update borrowers set category='Condonation' where id = 1188");
        DB::update("update borrowers set category='Condonation' where id = 1143");
        DB::update("update borrowers set category='Condonation' where id = 1977");
        DB::update("update borrowers set category='Condonation' where id = 2197");
        DB::update("update borrowers set category='Condonation' where id = 2265");
        DB::update("update borrowers set category='Condonation' where id = 1286");
        DB::update("update borrowers set category='Condonation' where id = 1084");
        DB::update("update borrowers set category='Condonation' where id = 1362");
        DB::update("update borrowers set category='Condonation' where id = 1400");
        DB::update("update borrowers set category='Condonation' where id = 1315");
        DB::update("update borrowers set category='Condonation' where id = 1456");
        DB::update("update borrowers set category='Condonation' where id = 1332");
        DB::update("update borrowers set category='Condonation' where id = 1593");
        DB::update("update borrowers set category='Condonation' where id = 1549");
        DB::update("update borrowers set category='Condonation' where id = 1494");
        DB::update("update borrowers set category='Condonation' where id = 1854");
        DB::update("update borrowers set category='Condonation' where id = 1652");
        DB::update("update borrowers set category='Condonation' where id = 1493");
        DB::update("update borrowers set category='Condonation' where id = 1594");
        DB::update("update borrowers set category='Condonation' where id = 2269");
        DB::update("update borrowers set category='Condonation' where id = 1614");
        DB::update("update borrowers set category='Condonation' where id = 2280");
        DB::update("update borrowers set category='Condonation' where id = 1451");

        DB::update("update borrowers set prob_fas= 7 where id= 1049");
        DB::update("update borrowers set prob_fas= 6 where id= 1015");
        DB::update("update borrowers set prob_fas= 7 where id= 1073");
        DB::update("update borrowers set prob_fas= 7 where id= 1544");
        DB::update("update borrowers set prob_fas= 8 where id= 1405");
        DB::update("update borrowers set prob_fas= 8 where id= 1399");
        DB::update("update borrowers set prob_fas= 8 where id= 1608");
        DB::update("update borrowers set prob_fas= 8 where id= 1612");
        DB::update("update borrowers set prob_fas= 7 where id= 1654");
        DB::update("update borrowers set prob_fas= 5 where id= 1495");
        DB::update("update borrowers set prob_fas= 7 where id= 1825");
        DB::update("update borrowers set prob_fas= 8 where id= 1606");
        DB::update("update borrowers set prob_fas= 8 where id= 1685");
        DB::update("update borrowers set prob_fas= 7 where id= 1542");
        DB::update("update borrowers set prob_fas= 6 where id= 2131");
        DB::update("update borrowers set prob_fas= 4 where id= 1837");
        DB::update("update borrowers set prob_fas= 7 where id= 1512");
        DB::update("update borrowers set prob_fas= 8 where id= 1626");
        DB::update("update borrowers set prob_fas= 8 where id= 1818");
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
