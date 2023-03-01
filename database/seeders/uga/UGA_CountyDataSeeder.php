<?php

use Illuminate\Database\Seeder;

class UGA_CountyDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $country_code = 'UGA';
        
       
        DB::table('master_data')->insert([

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Labwor County', 'data_code' => 'labwor', 'parent_data_code' => 'abim',  'status' => 'enabled', 'data_type' => 'address'], 
        
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'East Moyo County', 'data_code' => 'east_moyo', 'parent_data_code' => 'adjumani',  'status' => 'enabled', 'data_type' => 'address'],
    

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Agago County', 'data_code' => 'agago', 'parent_data_code' => 'agago',  'status' => 'enabled', 'data_type' => 'address'], 

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ajuri County', 'data_code' => 'ajuri', 'parent_data_code' => 'alebtong',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Moroto County', 'data_code' => 'moroto', 'parent_data_code' => 'alebtong',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kioga County', 'data_code' => 'kioga', 'parent_data_code' => 'amolatar',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Upe County', 'data_code' => 'upe', 'parent_data_code' => 'amudat',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Amuria County', 'data_code' => 'amuria', 'parent_data_code' => 'amuria',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kapelebyong County', 'data_code' => 'kapelebyong', 'parent_data_code' => 'amuria',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kilak County', 'data_code' => 'kilak', 'parent_data_code' => 'amuru',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kwania County', 'data_code' => 'kwania', 'parent_data_code' => 'apac',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Maruzi County', 'data_code' => 'maruzi', 'parent_data_code' => 'apac',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Arua Municipal Council', 'data_code' => 'arua', 'parent_data_code' => 'arua',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ayivu', 'data_code' => 'ayivu', 'parent_data_code' => 'ayivu',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Madi-Okollo County', 'data_code' => 'madi-okollo', 'parent_data_code' => 'arua',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Terego County', 'data_code' => 'terego', 'parent_data_code' => 'arua',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Vurra County', 'data_code' => 'vurra', 'parent_data_code' => 'arua',  'status' => 'enabled', 'data_type' => 'address'],

          ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Budaka County', 'data_code' => 'budaka', 'parent_data_code' => 'budaka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Iki-Iki County', 'data_code' => 'iki-iki', 'parent_data_code' => 'budaka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Manjiya County', 'data_code' => 'manjiya', 'parent_data_code' => 'bududa',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukooli County', 'data_code' => 'bukooli', 'parent_data_code' => 'bugiri',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buhweju County', 'data_code' => 'buhweju', 'parent_data_code' => 'buhweju',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buikwe County', 'data_code' => 'buikwe', 'parent_data_code' => 'buikwe',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukedea County', 'data_code' => 'bukedea', 'parent_data_code' => 'bukedea',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukomansimbi County', 'data_code' => 'bukomansimbi', 'parent_data_code' => 'bukomansimbi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bulambuli County', 'data_code' => 'bulambuli', 'parent_data_code' => 'bulambuli',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buliisa County', 'data_code' => 'buliisa', 'parent_data_code' => 'buliisa',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bughendera County', 'data_code' => 'bughendera', 'parent_data_code' => 'bundibugyo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bwamba County', 'data_code' => 'bwamba', 'parent_data_code' => 'bundibugyo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bushenyi-Ishaka Municipal Council', 'data_code' => 'bushenyi_ishaka', 'parent_data_code' => 'bushenyi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Igara County', 'data_code' => 'igara', 'parent_data_code' => 'bushenyi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busia Municipal Council', 'data_code' => 'busia', 'parent_data_code' => 'busia',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Samia-Bugwe County', 'data_code' => 'samia-bugwe', 'parent_data_code' => 'busia',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bunyole County', 'data_code' => 'bunyole', 'parent_data_code' => 'butaleja',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Butambala County', 'data_code' => 'butambala', 'parent_data_code' => 'butambala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buvuma Islands County', 'data_code' => 'buvuma_islands', 'parent_data_code' => 'buvuma',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Budiope East County', 'data_code' => 'budiope', 'parent_data_code' => 'buyende',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Budiope West County', 'data_code' => 'budiope_west', 'parent_data_code' => 'buyende',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Dokolo County', 'data_code' => 'dokolo', 'parent_data_code' => 'dokolo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Aswa County', 'data_code' => 'aswa', 'parent_data_code' => 'gulu',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Gulu Municipal Council', 'data_code' => 'gulu', 'parent_data_code' => 'gulu',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Omoro County', 'data_code' => 'omoro', 'parent_data_code' => 'gulu',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bugahya County', 'data_code' => 'bugahya', 'parent_data_code' => 'hoima',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buhaguzi County', 'data_code' => 'buhaguzi', 'parent_data_code' => 'hoima',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Homia Municipal Council', 'data_code' => 'hoima', 'parent_data_code' => 'hoima',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ibanda County', 'data_code' => 'ibanda', 'parent_data_code' => 'ibanda',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bugweri County', 'data_code' => 'bugweri', 'parent_data_code' => 'iganga',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Iganga Municipal Council', 'data_code' => 'iganga', 'parent_data_code' => 'iganga',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kigulu County', 'data_code' => 'kigulu', 'parent_data_code' => 'iganga',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukanga County', 'data_code' => 'bukanga', 'parent_data_code' => 'isingiro',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Isingiro County', 'data_code' => 'isingiro', 'parent_data_code' => 'isingiro',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Butembe County', 'data_code' => 'butembe', 'parent_data_code' => 'jinja',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Jinja Municipal Council', 'data_code' => 'jinja', 'parent_data_code' => 'jinja',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kagoma County', 'data_code' => 'Kagoma', 'parent_data_code' => 'jinja',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Dodoth East County', 'data_code' => 'dodoth_east', 'parent_data_code' => 'kaabong',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Dodoth West County', 'data_code' => 'dodoth_west', 'parent_data_code' => 'kaabong',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kabale Municipal Council', 'data_code' => 'kabale', 'parent_data_code' => 'kabale',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ndorwa County', 'data_code' => 'ndorwa', 'parent_data_code' => 'kabale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rubanda County', 'data_code' => 'rubanda', 'parent_data_code' => 'kabale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rukiga County', 'data_code' => 'rukiga', 'parent_data_code' => 'kabale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bunyangabu County', 'data_code' => 'bunyangabu', 'parent_data_code' => 'kabarole',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Burahya County', 'data_code' => 'burahya', 'parent_data_code' => 'kabarole',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Fort Portal Municipal Council', 'data_code' => 'fort_portal', 'parent_data_code' => 'kabarole',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakasongola County', 'data_code' => 'nakasongola', 'parent_data_code' => 'kabarole',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kalaki County', 'data_code' => 'kalaki', 'parent_data_code' => 'kaberamaido',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kaberamaido County', 'data_code' => 'kaberamaido', 'parent_data_code' => 'kaberamaido',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyamuswa County', 'data_code' => 'kyamuswa', 'parent_data_code' => 'kalangala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bujumba County', 'data_code' => 'bujumba', 'parent_data_code' => 'kalangala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kalungu County', 'data_code' => 'kalungu', 'parent_data_code' => 'kalungu',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kampala Capital City', 'data_code' => 'kampala_capital_city', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bugabula County', 'data_code' => 'bugabula', 'parent_data_code' => 'kamuli',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buzaaya County', 'data_code' => 'buzaaya', 'parent_data_code' => 'kamuli',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kibale County', 'data_code' => 'Kibale', 'parent_data_code' => 'kamwenge',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Tingey County', 'data_code' => 'tingey', 'parent_data_code' => 'kapchorwa',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukonzo County', 'data_code' => 'bukonzo', 'parent_data_code' => 'kasese',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busongora County', 'data_code' => 'busongora', 'parent_data_code' => 'kasese',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kasese Municipal Council', 'data_code' => 'kasese', 'parent_data_code' => 'kasese',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Toroma County', 'data_code' => 'toroma', 'parent_data_code' => 'katakwi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Usuk County', 'data_code' => 'usuk', 'parent_data_code' => 'katakwi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bbaale County', 'data_code' => 'bbaale', 'parent_data_code' => 'kayunga',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ntenjeru County', 'data_code' => 'ntenjeru', 'parent_data_code' => 'kayunga',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bugangaizi County', 'data_code' => 'bugangaizi', 'parent_data_code' => 'kibaale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bugangaizi East County', 'data_code' => 'bugangaizi_east', 'parent_data_code' => 'kibaale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buyaga County', 'data_code' => 'buyaga', 'parent_data_code' => 'kibaale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buyaga West County', 'data_code' => 'buyaga_west', 'parent_data_code' => 'kibaale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buyanja County', 'data_code' => 'buyanja', 'parent_data_code' => 'kibaale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kiboga East County', 'data_code' => 'kiboga_east', 'parent_data_code' => 'kiboga',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kibuku County', 'data_code' => 'kibuku', 'parent_data_code' => 'kibuku',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kazo County', 'data_code' => 'kazo', 'parent_data_code' => 'kiruhura',  'status' => 'enabled', 'data_type' => 'address'],

        
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nyabushozi County', 'data_code' => 'nyabushozi', 'parent_data_code' => 'kiruhura',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kibanda County', 'data_code' => 'kibanda', 'parent_data_code' => 'kiryandongo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bufumbira County', 'data_code' => 'bufumbira', 'parent_data_code' => 'kisoro',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Chua County', 'data_code' => 'chua', 'parent_data_code' => 'kitgum',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Koboko County', 'data_code' => 'koboko', 'parent_data_code' => 'koboko',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kole County', 'data_code' => 'kole', 'parent_data_code' => 'kole',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Jie County', 'data_code' => 'jie', 'parent_data_code' => 'kotido',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kumi County', 'data_code' => 'kumi', 'parent_data_code' => 'kumi',  'status' => 'enabled', 'data_type' => 'address'],
        

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kween County', 'data_code' => 'kween', 'parent_data_code' => 'kween',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kiboga West County', 'data_code' => 'kiboga_west', 'parent_data_code' => 'kyankwanzi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyaka County', 'data_code' => 'kyaka', 'parent_data_code' => 'kyegegwa',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mwenge County', 'data_code' => 'mwenge', 'parent_data_code' => 'kyenjojo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Lamwo County', 'data_code' => 'lamwo', 'parent_data_code' => 'lamwo',  'status' => 'enabled', 'data_type' => 'address'],

       
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Erute County', 'data_code' => 'erute', 'parent_data_code' => 'lira',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Luuka County', 'data_code' => 'luuka', 'parent_data_code' => 'luuka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bamunanika County', 'data_code' => 'bamunanika', 'parent_data_code' => 'luweero',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Katikamu County', 'data_code' => 'Katikamu', 'parent_data_code' => 'luweero',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukoto County', 'data_code' => 'bukoto', 'parent_data_code' => 'lwengo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kabula County', 'data_code' => 'kabula', 'parent_data_code' => 'lyantonde',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bubulo County', 'data_code' => 'bubulo', 'parent_data_code' => 'manafwa',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukoto Central County', 'data_code' => 'bukoto_central', 'parent_data_code' => 'masaka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukoto East County', 'data_code' => 'bukoto_east', 'parent_data_code' => 'masaka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Masaka Municipal Council', 'data_code' => 'masaka', 'parent_data_code' => 'masaka',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bujenje County', 'data_code' => 'bujenje', 'parent_data_code' => 'masindi',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buruuli County', 'data_code' => 'buruuli', 'parent_data_code' => 'masindi',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Masindi Municipal Council', 'data_code' => 'masindi', 'parent_data_code' => 'masindi',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bungokho County', 'data_code' => 'bungokho', 'parent_data_code' => 'mbale',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mbale Municipal Council', 'data_code' => 'mbale', 'parent_data_code' => 'mbale',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kashari County', 'data_code' => 'kashari', 'parent_data_code' => 'mbarara',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mbarara Municipal Council', 'data_code' => 'mbarara', 'parent_data_code' => 'mbarara',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ruhinda County', 'data_code' => 'ruhinda', 'parent_data_code' => 'mitooma',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busujju County', 'data_code' => 'busujju', 'parent_data_code' => 'mityana',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mityana County', 'data_code' => 'mityana', 'parent_data_code' => 'mityana',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Matheniko County', 'data_code' => 'matheniko', 'parent_data_code' => 'moroto',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Moroto Municipal Council', 'data_code' => 'moroto', 'parent_data_code' => 'moroto',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Obongi County', 'data_code' => 'obongi', 'parent_data_code' => 'moyo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'West Moyo County', 'data_code' => 'west_moyo', 'parent_data_code' => 'moyo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mawokota County', 'data_code' => 'mawokota', 'parent_data_code' => 'mpigi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Buwekula County', 'data_code' => 'buwekula', 'parent_data_code' => 'mubende',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kasambya County', 'data_code' => 'kasambya', 'parent_data_code' => 'mubende',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kassanda County', 'data_code' => 'Kassanda', 'parent_data_code' => 'mubende',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mukono County', 'data_code' => 'mukono', 'parent_data_code' => 'mukono',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mukono Municipal Council', 'data_code' => 'mukono', 'parent_data_code' => 'mukono',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakifuma Council', 'data_code' => 'nakifuma', 'parent_data_code' => 'mukono',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakaseke North County', 'data_code' => 'nakaseke_north', 'parent_data_code' => 'nakaseke',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakaseke South County', 'data_code' => 'nakaseke_south', 'parent_data_code' => 'nakaseke',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Budyebo County', 'data_code' => 'budyebo', 'parent_data_code' => 'nakasongola',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakasongola County', 'data_code' => 'nakasongola', 'parent_data_code' => 'nakasongola',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukooli Island County', 'data_code' => 'bukooli_island', 'parent_data_code' => 'namayingo',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bukooli South County', 'data_code' => 'bukooli_south', 'parent_data_code' => 'namayingo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busiki County', 'data_code' => 'busiki', 'parent_data_code' => 'namutumba',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bokora County', 'data_code' => 'bokora', 'parent_data_code' => 'napak',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Padyere County', 'data_code' => 'padyere', 'parent_data_code' => 'nebbi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Jonam County', 'data_code' => 'jonam', 'parent_data_code' => 'nebbi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ngora County', 'data_code' => 'ngora', 'parent_data_code' => 'ngora',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ntoroko County', 'data_code' => 'ntoroko', 'parent_data_code' => 'ntoroko',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kajara County', 'data_code' => 'kajara', 'parent_data_code' => 'ntungamo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ntungamo Municipal Council', 'data_code' => 'ntungamo', 'parent_data_code' => 'ntungamo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ruhaama County', 'data_code' => 'ruhaama', 'parent_data_code' => 'ntungamo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rushenyi County', 'data_code' => 'rushenyi', 'parent_data_code' => 'ntungamo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nwoya County', 'data_code' => 'nwoya', 'parent_data_code' => 'nwoya',  'status' => 'enabled', 'data_type' => 'address'],  


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Otuke County', 'data_code' => 'otuke', 'parent_data_code' => 'otuke',  'status' => 'enabled', 'data_type' => 'address'],
        
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Oyam County', 'data_code' => 'oyam', 'parent_data_code' => 'oyam',  'status' => 'enabled', 'data_type' => 'address'],
        
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Aruu County', 'data_code' => 'aruu', 'parent_data_code' => 'pader',  'status' => 'enabled', 'data_type' => 'address'], 
        
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Agule County', 'data_code' => 'agule', 'parent_data_code' => 'pallisa',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Butebo County', 'data_code' => 'butebo', 'parent_data_code' => 'pallisa',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Pallisa County', 'data_code' => 'pallisa', 'parent_data_code' => 'pallisa',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kakuuto County', 'data_code' => 'kakuuto', 'parent_data_code' => 'rakai',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kooki County', 'data_code' => 'kooki', 'parent_data_code' => 'rakai',  'status' => 'enabled', 'data_type' => 'address'],  

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyotera County', 'data_code' => 'kyotera', 'parent_data_code' => 'rakai',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bunyaruguru County', 'data_code' => 'bunyaruguru', 'parent_data_code' => 'rubirizi',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Katerera County', 'data_code' => 'katerera', 'parent_data_code' => 'rubirizi',  'status' => 'enabled', 'data_type' => 'address'],  

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rubabo County', 'data_code' => 'rubabo', 'parent_data_code' => 'rukungiri',  'status' => 'enabled', 'data_type' => 'address'], 

          ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rujumnura County', 'data_code' => 'rujumnura', 'parent_data_code' => 'rukungiri',  'status' => 'enabled', 'data_type' => 'address'], 

          ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rukungiri Municipal Council', 'data_code' => 'rujumnura', 'parent_data_code' => 'rukungiri',  'status' => 'enabled', 'data_type' => 'address'], 


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Lwemiyaga County', 'data_code' => 'lwemiyaga', 'parent_data_code' => 'sembabule',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mawogola County', 'data_code' => 'mawogola', 'parent_data_code' => 'sembabule',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kasilo County', 'data_code' => 'kasilo', 'parent_data_code' => 'serere',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Serere County', 'data_code' => 'serere', 'parent_data_code' => 'serere',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Sheema County', 'data_code' => 'sheema', 'parent_data_code' => 'sheema',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Budadiri County', 'data_code' => 'budadiri', 'parent_data_code' => 'sironko',  'status' => 'enabled', 'data_type' => 'address'],
   
         
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Soroti Municipal Council', 'data_code' => 'soroti', 'parent_data_code' => 'soroti',  'status' => 'enabled', 'data_type' => 'address'],

         
        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Soroti County', 'data_code' => 'soroti', 'parent_data_code' => 'soroti',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Tororo County', 'data_code' => 'tororo', 'parent_data_code' => 'tororo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Tororo Municipal Council', 'data_code' => 'tororo', 'parent_data_code' => 'tororo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'West Budama County', 'data_code' => 'west_budama', 'parent_data_code' => 'tororo',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busiro County', 'data_code' => 'busiro', 'parent_data_code' => 'wakiso',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Entebbe Municipal Council', 'data_code' => 'entebbe', 'parent_data_code' => 'wakiso',  'status' => 'enabled', 'data_type' => 'address'],


         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyadondo County', 'data_code' => 'kyadondo', 'parent_data_code' => 'wakiso',  'status' => 'enabled', 'data_type' => 'address'],
  
         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Aringa County', 'data_code' => 'aringa', 'parent_data_code' => 'yumbe',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Okoro County', 'data_code' => 'okoro', 'parent_data_code' => 'zombo',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nakawa Division', 'data_code' => 'nakawa_division', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Central Division', 'data_code' => 'central_division', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Makindye', 'data_code' => 'makindye', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Makindye Division', 'data_code' => 'makindye_division', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Wakiso', 'data_code' => 'wakiso', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Ntenjeru', 'data_code' => 'ntenjeru', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

         ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyadondo', 'data_code' => 'kyadondo', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Rubaga Division', 'data_code' => 'rubaga_division', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bubale', 'data_code' => 'bubale', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Bubale', 'data_code' => 'bubale', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Mukono municpality', 'data_code' => 'mukono_municpality', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Nangabo', 'data_code' => 'nangabo', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busiro', 'data_code' => 'busiro', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],


        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Busiro', 'data_code' => 'busiro', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address'],

        ['country_code' => $country_code, 'data_key' => 'county', 'data_value' => 'Kyegegwa', 'data_code' => 'kyegegwa', 'parent_data_code' => 'kampala',  'status' => 'enabled', 'data_type' => 'address']
        
        ]);
    }
}
