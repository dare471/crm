<?php
namespace Database\Seeders;
use App\Models\Geo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class GeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  $faker = \Faker\Factory::create();
        ini_set('memory_limit', '-1');
        $json = file_get_contents('/var/www/crm/final_2020.geojson');
        $obj = json_decode($json);
        foreach($obj->features as $value){
            Geo::create([
                'fid'=>$value->properties->fid,
                'owner' => $value->properties->owner,
                'cult' => $value->properties->cult,
                'region' => $value->properties->region,
                'district' => $value->properties->district,
                'area' => $value->properties->area,
                'year' => $value->properties->year,
                'kad_number' => $value->properties->kad_number,
                'title' => $value->properties->title,
                'geometry' => json_encode($value->geometry->coordinates[0])
            ]);
        }
    }
}