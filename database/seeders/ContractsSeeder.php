<?php

namespace Database\Seeders;
use App\Model\Contracts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Contragents;
use App\Resource\Resource;

class ContractsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $select = DB::table('contragents')->get();
        foreach ($select as $selecs) {
        $iin = is_array($selecs->IIN_BIN);
        $faker = \Faker\Factory::create();
        $curl = curl_init();
            curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://192.168.1.232:8080/api/contracts/?iin='.$iin,
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
                foreach ($array['contracts'] as $id) {
                Contracts::create([
                    'ContractGuid'=>$id['ContractGuid'],
                    'OrganizationName' => $id['OrganizationName'],
                    'ContragentName' => $id['ContragentName'],
                    'ManagerGuid' => $id['ManagerGuid'],
                    'RegionGuid' => $id['RegionGuid'],
                    'SeasonGuid'=>$id['SeasonGuid'],
                    'WarehouseGuid'=>$id['WarehouseGuid'],
                    'Currency'=>$id['Currency']
                ]); 
            } 
        }
    }
}
