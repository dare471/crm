<?php
ini_set('memory_limit', '-1');
$json = file_get_contents(__DIR__.'\final_2020.geojson');
$obj = json_decode($json);
//var_dump($obj->features[0]->properties);


foreach($obj->features as $value){

  echo $value;

}