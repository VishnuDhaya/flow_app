<?php

use Illuminate\Database\Seeder;

class UGA_AddressDataSeeder extends Seeder
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

        ['country_code' => $country_code, 'data_key' => 'region', 'data_value' => 'Central', 'data_code' => 'central', 'parent_data_code' => null,  'status' => 'enabled', 'data_type' => 'address'], 
    
        ['country_code' => $country_code, 'data_key' => 'region', 'data_value' => 'Eastern', 'data_code' => 'eastern', 'parent_data_code' => null,  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'region', 'data_value' => 'Northern', 'data_code' => 'northern', 'parent_data_code' => null,  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'region', 'data_value' => 'Western', 'data_code' => 'western', 'parent_data_code' => null,  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Buikwe', 'data_code' => 'buikwe', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bukomansimbi', 'data_code' => 'bukomansimbi', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Butambala', 'data_code' => 'butambala', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Buvuma', 'data_code' => 'buvuma', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Gomba', 'data_code' => 'gomba', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kalangala', 'data_code' => 'kalangala', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kalungu', 'data_code' => 'kalungu', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kampala', 'data_code' => 'kampala', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kayunga', 'data_code' => 'kayunga', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kiboga', 'data_code' => 'kiboga', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kyankwanzi', 'data_code' => 'kyankwanzi', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Luweero', 'data_code' => 'luweero', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Lwengo', 'data_code' => 'lwengo', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Lyantonde', 'data_code' => 'lyantonde', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Masaka', 'data_code' => 'masaka', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mityana', 'data_code' => 'mityana', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mpigi', 'data_code' => 'mpigi', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mubende', 'data_code' => 'mubende', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mukono', 'data_code' => 'mukono', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Nakaseke', 'data_code' => 'nakaseke', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Nakasongola', 'data_code' => 'nakasongola', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Rakai', 'data_code' => 'rakai', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Ssembabule', 'data_code' => 'ssembabule', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Wakiso', 'data_code' => 'wakiso', 'parent_data_code' => 'central',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Amuria', 'data_code' => 'amuria', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Budaka', 'data_code' => 'budaka', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bududa', 'data_code' => 'bududa', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bugiri', 'data_code' => 'bugiri', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bukedea', 'data_code' => 'bukedea', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bukwa', 'data_code' => 'bukwa', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bulambuli', 'data_code' => 'bulambuli', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Busia', 'data_code' => 'busia', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Butaleja', 'data_code' => 'butaleja', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Buyende', 'data_code' => 'buyende', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Iganga', 'data_code' => 'iganga', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Jinja', 'data_code' => 'jinja', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kaberamaido', 'data_code' => 'kaberamaido', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kaliro', 'data_code' => 'Kaliro', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kamuli', 'data_code' => 'kamuli', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kapchorwa', 'data_code' => 'kapchorwa', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Katakwi', 'data_code' => 'katakwi', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kibuku', 'data_code' => 'kibuku', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kumi', 'data_code' => 'kumi', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kween', 'data_code' => 'kween', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Luuka', 'data_code' => 'luuka', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Manafwa', 'data_code' => 'manafwa', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mayuge', 'data_code' => 'Mayuge', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mbale', 'data_code' => 'mbale', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Namayingo', 'data_code' => 'namayingo', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Namutumba', 'data_code' => 'namutumba', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Ngora', 'data_code' => 'ngora', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Pallisa', 'data_code' => 'pallisa', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Serere', 'data_code' => 'serere', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Sironko', 'data_code' => 'sironko', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Soroti', 'data_code' => 'soroti', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Tororo', 'data_code' => 'tororo', 'parent_data_code' => 'eastern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Abim', 'data_code' => 'abim', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Adjumani', 'data_code' => 'adjumani', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Agago', 'data_code' => 'agago', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Alebtong', 'data_code' => 'alebtong', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Amolatar', 'data_code' => 'amolatar', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Amudat', 'data_code' => 'amudat', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Amuru', 'data_code' => 'amuru', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Apac', 'data_code' => 'apac', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Arua', 'data_code' => 'arua', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 


        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Dokolo', 'data_code' => 'dokolo', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Gulu', 'data_code' => 'gulu', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kaabong', 'data_code' => 'kaabong', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kitgum', 'data_code' => 'kitgum', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Koboko', 'data_code' => 'koboko', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kole', 'data_code' => 'kole', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kotido', 'data_code' => 'kotido', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Lamwo', 'data_code' => 'lamwo', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Lira', 'data_code' => 'lira', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Maracha', 'data_code' => 'maracha', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Moroto', 'data_code' => 'moroto', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Moyo', 'data_code' => 'moyo', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Nakapiripirit', 'data_code' => 'Nakapiripirit', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Napak', 'data_code' => 'napak', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Nebbi', 'data_code' => 'nebbi', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Nwoya', 'data_code' => 'nwoya', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Otuke', 'data_code' => 'otuke', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Oyam', 'data_code' => 'oyam', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Pader', 'data_code' => 'pader', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Yumbe', 'data_code' => 'yumbe', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Zombo', 'data_code' => 'zombo', 'parent_data_code' => 'northern',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Buhweju', 'data_code' => 'buhweju', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Buliisa', 'data_code' => 'buliisa', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bundibugyo', 'data_code' => 'bundibugyo', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Bushenyi', 'data_code' => 'bushenyi', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Hoima', 'data_code' => 'hoima', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Ibanda', 'data_code' => 'ibanda', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Isingiro', 'data_code' => 'isingiro', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kabale', 'data_code' => 'kabale', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kabarole', 'data_code' => 'kabarole', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kamwenge', 'data_code' => 'kamwenge', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kanungu', 'data_code' => 'Kanungu', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kasese', 'data_code' => 'kasese', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kibaale', 'data_code' => 'kibaale', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kiruhura', 'data_code' => 'kiruhura', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kiryandongo', 'data_code' => 'kiryandongo', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kisoro', 'data_code' => 'kisoro', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kyegegwa', 'data_code' => 'kyegegwa', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Kyenjojo', 'data_code' => 'kyenjojo', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Masindi', 'data_code' => 'masindi', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mbarara', 'data_code' => 'mbarara', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Mitooma', 'data_code' => 'mitooma', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Ntoroko', 'data_code' => 'ntoroko', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Ntungamo', 'data_code' => 'ntungamo', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Rubirizi', 'data_code' => 'rubirizi', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Rukungiri', 'data_code' => 'rukungiri', 'parent_data_code' => 'western',  'status' => 'enabled', 'data_type' => 'address'], 

        ['country_code' => $country_code, 'data_key' => 'district', 'data_value' => 'Sheema', 'data_code' => 'sheema', 'parent_data_code' => 'western', 'status' => 'enabled',  'data_type' => 'address']


        ]);
    }
}
