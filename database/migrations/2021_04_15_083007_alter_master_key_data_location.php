<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterKeyDataLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("delete from master_data where data_key = 'location'");

        DB::table('master_data')->insert([

            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'banda', 'data_value' => 'Banda','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bombo', 'data_value' => 'Bombo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buddo', 'data_value' => 'Buddo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bugembe', 'data_value' => 'Bugembe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bugolobi', 'data_value' => 'Bugolobi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buikwe', 'data_value' => 'Buikwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bujjuko', 'data_value' => 'Bujjuko','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bukasa', 'data_value' => 'Bukasa','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bukerere', 'data_value' => 'Bukerere','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bukolooto', 'data_value' => 'Bukolooto','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bukoto', 'data_value' => 'Bukoto','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bulenga', 'data_value' => 'Bulenga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bulindo', 'data_value' => 'Bulindo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buloba', 'data_value' => 'Buloba','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bunamwaya', 'data_value' => 'Bunamwaya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bunga', 'data_value' => 'Bunga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'busaabala', 'data_value' => 'Busaabala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'busega', 'data_value' => 'Busega','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'busia', 'data_value' => 'Busia','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'busiika', 'data_value' => 'Busiika','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buwama', 'data_value' => 'Buwama','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buwambo', 'data_value' => 'Buwambo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'buziga', 'data_value' => 'Buziga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bwaise', 'data_value' => 'Bwaise','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bwera', 'data_value' => 'Bwera','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'bweyogerere', 'data_value' => 'Bweyogerere','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'cbd', 'data_value' => 'Cbd','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'entebbe', 'data_value' => 'Entebbe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ezeemoney office', 'data_value' => 'Ezeemoney Office','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'fortportal', 'data_value' => 'Fortportal','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'gangu', 'data_value' => 'Gangu','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'gayaza', 'data_value' => 'Gayaza','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ggaba', 'data_value' => 'Ggaba','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'hoima', 'data_value' => 'Hoima','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'iganga', 'data_value' => 'Iganga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'industrial area', 'data_value' => 'Industrial Area','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'jinja', 'data_value' => 'Jinja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kabalagala', 'data_value' => 'Kabalagala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kabale', 'data_value' => 'Kabale','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kagoma', 'data_value' => 'Kagoma','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kajjansi', 'data_value' => 'Kajjansi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kakiri', 'data_value' => 'Kakiri','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kalagi', 'data_value' => 'Kalagi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kalerwe', 'data_value' => 'Kalerwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kaliro', 'data_value' => 'Kaliro','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kalisizo', 'data_value' => 'Kalisizo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kampala', 'data_value' => 'Kampala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kamuli', 'data_value' => 'Kamuli','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kamwokya', 'data_value' => 'Kamwokya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kangulumira', 'data_value' => 'Kangulumira','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kansanga', 'data_value' => 'Kansanga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kanyanya', 'data_value' => 'Kanyanya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => '', 'data_value' => '','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kasanga', 'data_value' => 'Kasanga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kasangati', 'data_value' => 'Kasangati','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kasese', 'data_value' => 'Kasese','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kasokoso', 'data_value' => 'Kasokoso','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kasubi', 'data_value' => 'Kasubi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'katooke', 'data_value' => 'Katooke','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'katwe', 'data_value' => 'Katwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kawaala', 'data_value' => 'Kawaala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kawanda', 'data_value' => 'Kawanda','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kawempe', 'data_value' => 'Kawempe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kawooya', 'data_value' => 'Kawooya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kawuku', 'data_value' => 'Kawuku','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kayunga', 'data_value' => 'Kayunga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kazo', 'data_value' => 'Kazo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kibiri', 'data_value' => 'Kibiri','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiboga', 'data_value' => 'Kiboga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kibuli', 'data_value' => 'Kibuli','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiganda', 'data_value' => 'Kiganda','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kigoowa', 'data_value' => 'Kigoowa','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kigunga', 'data_value' => 'Kigunga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kikaaya', 'data_value' => 'Kikaaya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kikajjo', 'data_value' => 'Kikajjo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kikoni', 'data_value' => 'Kikoni','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kikugamwanga', 'data_value' => 'Kikugamwanga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kinaawa', 'data_value' => 'Kinaawa','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kinawataka', 'data_value' => 'Kinawataka','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kira', 'data_value' => 'Kira','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kireka', 'data_value' => 'Kireka','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kireku', 'data_value' => 'Kireku','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kirinya', 'data_value' => 'Kirinya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kirombe', 'data_value' => 'Kirombe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kisaasi', 'data_value' => 'Kisaasi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kisenyi', 'data_value' => 'Kisenyi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kisubi', 'data_value' => 'Kisubi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kisugu', 'data_value' => 'Kisugu','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kitala', 'data_value' => 'Kitala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiteezi', 'data_value' => 'Kiteezi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => '', 'data_value' => '','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kitemu', 'data_value' => 'Kitemu','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kitintale', 'data_value' => 'Kitintale','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiwanga', 'data_value' => 'Kiwanga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiwatule', 'data_value' => 'Kiwatule','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kiyunga', 'data_value' => 'Kiyunga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kololo', 'data_value' => 'Kololo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kulambiro', 'data_value' => 'Kulambiro','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyadondo', 'data_value' => 'Kyadondo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyaliwajala', 'data_value' => 'Kyaliwajala','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyambogo', 'data_value' => 'Kyambogo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyanja', 'data_value' => 'Kyanja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyebando', 'data_value' => 'Kyebando','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyengera', 'data_value' => 'Kyengera','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'kyetume', 'data_value' => 'Kyetume','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lugazi', 'data_value' => 'Lugazi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lukaya', 'data_value' => 'Lukaya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lungujja', 'data_value' => 'Lungujja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lusanja', 'data_value' => 'Lusanja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lutete', 'data_value' => 'Lutete','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'luweero', 'data_value' => 'Luweero','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'luzira', 'data_value' => 'Luzira','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'lyantonde', 'data_value' => 'Lyantonde','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'maganjo', 'data_value' => 'Maganjo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'makerere', 'data_value' => 'Makerere','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'makindye', 'data_value' => 'Makindye','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'masajja', 'data_value' => 'Masajja','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'masaka', 'data_value' => 'Masaka','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'masanafu', 'data_value' => 'Masanafu','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'matugga', 'data_value' => 'Matugga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mawanda ', 'data_value' => 'Mawanda ','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'maya', 'data_value' => 'Maya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mayirikiti', 'data_value' => 'Mayirikiti','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mayuge', 'data_value' => 'Mayuge','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mbale', 'data_value' => 'Mbale','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mbarara', 'data_value' => 'Mbarara','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mbuya', 'data_value' => 'Mbuya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mengo', 'data_value' => 'Mengo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mityana', 'data_value' => 'Mityana','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mpererwe', 'data_value' => 'Mpererwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mpigi', 'data_value' => 'Mpigi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mukono', 'data_value' => 'Mukono','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mulago', 'data_value' => 'Mulago','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mutundwe', 'data_value' => 'Mutundwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'mutungo', 'data_value' => 'Mutungo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'muyenga', 'data_value' => 'Muyenga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'naalya', 'data_value' => 'Naalya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nabbingo', 'data_value' => 'Nabbingo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nabweru', 'data_value' => 'Nabweru','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'naguru', 'data_value' => 'Naguru','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'najjera', 'data_value' => 'Najjera','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakasero', 'data_value' => 'Nakasero','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakasongola', 'data_value' => 'Nakasongola','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakawa', 'data_value' => 'Nakawa','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakawade', 'data_value' => 'Nakawade','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakawuka', 'data_value' => 'Nakawuka','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakifuma', 'data_value' => 'Nakifuma','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakulabye', 'data_value' => 'Nakulabye','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakuwande', 'data_value' => 'Nakuwande','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nakwero', 'data_value' => 'Nakwero','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namagoma', 'data_value' => 'Namagoma','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namanve', 'data_value' => 'Namanve','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namasuba', 'data_value' => 'Namasuba','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namboole', 'data_value' => 'Namboole','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namugongo', 'data_value' => 'Namugongo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namugoona', 'data_value' => 'Namugoona','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namugoona', 'data_value' => 'Namugoona','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'namuwongo', 'data_value' => 'Namuwongo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nankulabye', 'data_value' => 'Nankulabye','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nankuwadde', 'data_value' => 'Nankuwadde','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nansana', 'data_value' => 'Nansana','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nanziri', 'data_value' => 'Nanziri','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nasser', 'data_value' => 'Nasser','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nateete', 'data_value' => 'Nateete','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nazigo', 'data_value' => 'Nazigo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ndeeba', 'data_value' => 'Ndeeba','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ndejje', 'data_value' => 'Ndejje','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nsambya', 'data_value' => 'Nsambya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nsangi', 'data_value' => 'Nsangi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ntinda', 'data_value' => 'Ntinda','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'nyanama', 'data_value' => 'Nyanama','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'rubaga', 'data_value' => 'Rubaga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'salaama', 'data_value' => 'Salaama','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'salaama road', 'data_value' => 'Salaama Road','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'seeta', 'data_value' => 'Seeta','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'seguku', 'data_value' => 'Seguku','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'tororo', 'data_value' => 'Tororo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'ttula', 'data_value' => 'Ttula','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wakakiga', 'data_value' => 'Wakakiga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wakaliga', 'data_value' => 'Wakaliga','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wakiso', 'data_value' => 'Wakiso','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wampewo', 'data_value' => 'Wampewo','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wandegeya', 'data_value' => 'Wandegeya','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'wobulenzi', 'data_value' => 'Wobulenzi','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'zirobwe', 'data_value' => 'Zirobwe','status' => 'enabled','created_at' => now()],
            ['country_code'=> 'UGA', 'data_type' => 'address', 'data_key' => 'location', 'data_code' => 'zombo', 'data_value' => 'Zombo','status' => 'enabled','created_at' => now()],
        ]);

        DB::update("update address_info set field_8='bukolooto' where field_8='bukoloota'");
        DB::update("update address_info set field_8='bukolooto' where field_8='bukoloto'");
        DB::update("update address_info set field_8='busaabala' where field_8='busabala'");
        DB::update("update address_info set field_8='bweyogerere' where field_8='bweyogere'");
        DB::update("update address_info set field_8='ggaba' where field_8='gaba'");
        DB::update("update address_info set field_8='ggaba' where field_8='ganda'");
        DB::update("update address_info set field_8='kasese' where field_8='kaseese'");
        DB::update("update address_info set field_8='kanyanya' where field_8='kayanya'");
        DB::update("update address_info set field_8='kirinya' where field_8='kirinya bigo'");
        DB::update("update address_info set field_8='kiteezi' where field_8='kitezi'");
        DB::update("update address_info set field_8='kyanja' where field_8='kjanja'");
        DB::update("update address_info set field_8='lungujja' where field_8='lunguja'");
        DB::update("update address_info set field_8='luweero' where field_8='luwero'");
        DB::update("update address_info set field_8='matugga' where field_8='mattugga'");
        DB::update("update address_info set field_8='mityana' where field_8='mityanya'");
        DB::update("update address_info set field_8='mpererwe' where field_8='mpererewe'");
        DB::update("update address_info set field_8='Naalya' where field_8='nalyaa'");
        DB::update("update address_info set field_8='nansana' where field_8='nansanna'");
        DB::update("update address_info set field_8='nasser' where field_8='nasser road'");
        DB::update("update address_info set field_8='ndeeba' where field_8='ndeba'");
        DB::update("update address_info set field_8='nsangi' where field_8='nsanji'");

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
