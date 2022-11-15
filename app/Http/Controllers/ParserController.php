<?php

namespace App\Http\Controllers;

use App\Models\Parser;
use Illuminate\Http\Request;
use Validator;
use Symfony\Component\DomCrawler\Crawler;
use Adldap\AdldapInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ParserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    protected function pars() {
        $pages = array('1' =>5790, '2' =>5767, '3' =>5921, '4'=>5829, '5'=>5793, '6'=>5368, '7'=>5850);
        foreach ($pages as $key) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://margin.kz/enterprises/5790/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                '_ga_6K78G5811W=GS1.1.1643356468.3.1.1643360700.0; _ga=GA1.2.127029875.1643350264; _gid=GA1.2.2010639792.1643350265; _ym_uid=1643350266809805971; _ym_d=1643350266; _ym_isad=2; csrftoken=wIH5nw8ODlGi3kugKQy3GMd8Esxc2A4u; sessionid=1degiawcoavvd5qp1mfdsts9j3c561pt; _ym_visorc=w'
            ),
          ));

        $res = curl_exec($curl);
        curl_close($curl);
     
        $dom = new \DomDocument();
        @ $dom->loadHTML('<?xml encoding="utf-8" ?>' . $res);
        $tags=$dom->getElementsByTagName('td');
        $arr=array();
        $i='0';
         foreach($tags as $h2) {
            $arr[$i++]=$h2->textContent;
            if($i==7){
                break;
            }          
        }
        var_dump($arr);
        DB::connection('CRM_DWH')->insert('insert into PARS (NAME_1, NAME_2, NAME_3, NAME_4, NAME_5, NAME_6) values (?, ?, ?, ?, ?, ?)', $arr);
        }
    }  
    
    protected function eldala(Request $request) {
        //return $request->link;
        $xml = file_get_contents("https://eldala.kz/dannye/kompanii?s=&c[0]=146"); 
        $dom = new \DOMDocument();
        @ $dom->loadHTML('<?xml encoding="utf-8"?>' . $xml);
        $finders = new \DOMXPath($dom);
        foreach($finders->query('//a/@data-urls') as $node) {
            $ll=trim($node->value, '["');
            $lll=trim($ll, '"]');
            $array_link_ex=explode('","', $lll);
            $array_link=array_push($array_link_ex, "https://eldala.kz/dannye/kompanii?s=&c[0]=146");
           // print_r($array_link_ex);
        }
        foreach ($array_link_ex as $links) {
         //  echo $iconv=urldecode($links);
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request->link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          ));

        $res = curl_exec($curl);
        curl_close($curl);
        $dom = new \DOMDocument();
       @ $dom->loadHTML('<?xml encoding="utf-8"?>' . $res);
        $finders = new \DOMXPath($dom);
        $arrayv=array();
        $arrayt=array();
        $v=0;
        $t=0;
        foreach($finders->query('//div[@class="material_item__inner"]/a/@href|//div[@class="material_item__link"]') as $node) {
            $arrayt[$t++]=$node->value;
        }
        foreach($finders->query('//div[@class="material_item__title"]') as $nodes) {
            $arrayv[$v++]=$nodes->nodeValue;
        }
        $r=0;
        $rr=array();
        $rrr=count($arrayv)/2;
       // print_r($arrayv);
        while ($r<$rrr) {
            $rr=array(
                "NAME" => $arrayv[$r],
                "URL" => $arrayt[$r]
            );
            $r++;
            DB::connection('CRM_DWH')->table('PARS_SPR')->insert([
                'NAME' => $rr['NAME'],
                'URL' => $rr['URL'],
                'TYPE'=> $request->type
            ]);
            }
            return "Все залито! Нажмите на ссылку http://192.168.1.16/api/parser_list что бы загрузить остальные данные по компаниям";
       }
       
    protected function eldaladetail() { 
        $table_p=DB::connection('CRM_DWH')
        ->table('PARS_SPR')
        ->select('NAME','URL')
        ->get();
        //return $table_p;
        foreach ($table_p as $data) {
            $url=$data->URL;
            $name=$data->NAME;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
              ));
    
            $res = curl_exec($curl);
            curl_close($curl);
            $dom = new \DOMDocument();
            @ $dom->loadHTML('<?xml encoding="utf-8" ?>' . $res);
            $finders = new \DOMXPath($dom);
            foreach($finders->query('//h1[@class="articletitle"]') as $node) {
                $node->nodeValue;
            }
            foreach($finders->query('//div[@class="page_company__head__desc"]') as $nodes) {
               $activity=$nodes->nodeValue;
            }
            foreach($finders->query('//div[@class="page_company__tab_content"]') as $detail) {
               $description=$detail->nodeValue;
             }
             foreach($finders->query('/html/body/section[2]/section/section/section[1]/main/div[2]/div/div/div[2]/div[1]/div[1]/span') as $home) {
               $region=$home->nodeValue;
             }
             foreach($finders->query('/html/body/section[2]/section/section/section[1]/main/div[2]/div/div/div[2]/div[1]/div[2]/span') as $phone) {
               $phone=$phone->nodeValue;
             }
             foreach($finders->query('/html/body/section[2]/section/section/section[1]/main/div[2]/div/div/div[2]/div[1]/div[3]/span') as $email) {
                $email=$email->nodeValue;
              }
                  $ret=DB::connection('CRM_DWH')
                  ->table('PARS_SPR')
                  ->where('NAME', $name)
                  ->update([
                      'ACTIVITY' => $activity,
                      'DESCRIPTION' => $description,
                      'REGION' => $region,
                      'PHONE' => $phone,
                      //'SITE_S' => $site,
                      //'ACTIVE_F' => $mln
                  ]); 
            }
            return "Данные залиты проверьте в БД - CRM_DWH, таблица";  
    }    
    public function qoldau(){
     
    }
    public function margin(){
        $i=1;
        while($i<309){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://margin.kz/enterprises/?page='.$i.'',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    '_ga_6K78G5811W=GS1.1.1643356468.3.1.1643360700.0; _ga=GA1.2.127029875.1643350264; _gid=GA1.2.2010639792.1643350265; _ym_uid=1643350266809805971; _ym_d=1643350266; _ym_isad=2; csrftoken=wIH5nw8ODlGi3kugKQy3GMd8Esxc2A4u; sessionid=1degiawcoavvd5qp1mfdsts9j3c561pt; _ym_visorc=w'
                ),
            ));
            $k=1;
            $res = curl_exec($curl);
            curl_close($curl);
            $dom = new \DOMDocument();
            @ $dom->loadHTML('<?xml encoding="utf-8" ?>' . $res);
            $finders = new \DOMXPath($dom);
            while($k<21){
                foreach($finders->query('/html/body/div[1]/div/div[2]/div/div[2]/div[2]/div/div/table/tbody/tr['.$k.']/td[1]') as $node) {
                    $name=$node->nodeValue;
                }
                foreach($finders->query('/html/body/div[1]/div/div[2]/div/div[2]/div[2]/div/div/table/tbody/tr['.$k.']/td[2]') as $node) {
                    $region=$node->nodeValue;
                }
                foreach($finders->query('/html/body/div[1]/div/div[2]/div/div[2]/div[2]/div/div/table/tbody/tr['.$k.']/td[3]') as $node) {
                    $type=$node->nodeValue;
                }          
                echo "</br>".$k++."</br>NAME - ".$name;
                $ret=DB::connection('CRM_DWH')
                  ->table('PARS')
                  ->insert([
                      'NAME_1' => $name,
                      'NAME_2' => $region,
                      'NAME_3' => $type
                  ]); 
            }
            echo "</br>Page-".$i++;
        }
    }
}
