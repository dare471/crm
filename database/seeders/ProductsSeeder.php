<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
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
          CURLOPT_URL => 'http://192.168.1.232:8000/api/products/',
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
        foreach ($array as $id) {
            Products::create([
                'product_name'=>$id['product'],
                'quantity' => $id['manufacture'],
                'manufacturer_guid' =>$id['product_id'],
                'price' =>$id['price'],
                'productcategory_guid'=>$id['category']
            ]); 
        } 
    }
}
