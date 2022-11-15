<?php

namespace Database\Seeders;
use App\Models\Manufacture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ManufactureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://192.168.1.232:8080/api/manufacture/',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        
        $array=json_decode($response, true);
        
        //var_dump($array['manufactures']);
        
        //print_r($array['manufactures']);
        
        foreach ($array['manufactures'] as $id) {
            Manufacture::create([
                'ManufacturerGuid'=>$id['ManufacturerGuid'],
                'Manufacture' => $id['Manufacture']
                ]); 
        } 
    }
}
