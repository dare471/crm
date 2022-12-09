<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use App\Http\Resources\ClientsFieldsPolygonResource;
use App\Http\Resources\MongoExample;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function UserSPR($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT [ID]
            ,CONVERT(VARCHAR(1000), GUID, 1) as GUID
            ,[NAIMENOVANIE]
            ,[DIREKTSYA]
            ,CH.[DOLZHNOST]
            ,[SOSTOYANIE]
            ,[PODRAZDELENIE]
            ,[ADRES_E_P]
            ,[TELEFON]
            ,[TELEGRAM_ID] FROM [CRM_DWH].[dbo].[CRM_USERS] AS CU
            INNER JOIN [CRM_DWH].[dbo].[CRM_HR] AS CH ON CH.SOTRUDNIK_GUID=CU.FIZICHESKOE_LITSO_GUID
            WHERE [TELEGRAM_ID]!=0 AND[NAIMENOVANIE]!='telegrambot' AND [SOSTOYANIE]!='Увольнение'  ORDER BY [NAIMENOVANIE] ASC");
        return response($query);
    }

    public function UserSPRTable($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT CONVERT(NVARCHAR(MAX), cu.GUID, 1)as USERS_GUID,
        cu.ID, 
        cu.TELEGRAM_ID,
        NAIMENOVANIE AS FULL_NAME, 
        DIREKTSYA AS DIRECTION, 
        DOLZHNOST AS POSITION, 
        ADRES_E_P AS EMAIL, 
        TELEFON AS PHONE, 
        PODRAZDELENIE AS SUBDIVISION, 
        CRM_CATO  
        FROM CRM_USERS cu 
        LEFT JOIN users u on u.email = cu.ADRES_E_P 
        WHERE u.id = $id");
        return response($query);
    }
    
    //user api 
    public function UserProfile($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $user_inf = $dbconn->select("SELECT [ID]
        ,CONVERT(VARCHAR(1000), GUID, 1) as GUID
        ,[NAIMENOVANIE]
        ,[DIREKTSYA]
        ,CH.[DOLZHNOST]
        ,[ADRES_E_P]
        ,[TELEFON]
        ,[DATA_ROZHDENIYA]
        ,[PODRAZDELENIE]
        ,[SOSTOYANIE]
        ,[POL]
        ,[VOZRAST]
        ,[STAZH]
        ,[TELEGRAM_ID] FROM [CRM_DWH].[dbo].[CRM_USERS] AS CU 
        INNER JOIN [CRM_DWH].[dbo].[CRM_HR] AS CH ON CH.SOTRUDNIK_GUID=CU.FIZICHESKOE_LITSO_GUID
        WHERE [GUID]=$id ORDER BY [NAIMENOVANIE] ASC");
        return response()->json([
            'user_inf' => $user_inf,
        ]);
    }



///Maps api start 
    // Route:: /country || Method:: GET
    public function Region()
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT TOP (14) 'region' as type,[ID] as id
            ,[NAME] as name
            ,SUBSTRING([KATO], 0, 3) AS cato 
            ,[POPULATION_AREA] as population_area
            ,[POPULATION_CITY] as population_city
            ,[POPULATION_VILLAGE] as population_village
            ,[GEOMETRY_RINGS] as geometry_rings
            ,[KLKOD] as klkod
        FROM [CRM_DWH].[dbo].[CRM_AISGZK_OBLAST_GEO]
        ");
        return response()->json([
            'success' => true,
            'data' => $query
        ]);
    }

   public function MongoDb($cato){
        $cato = (int)$cato;
        $query = DB::connection("mongodb")->table("regionlasticC")
        ->where('REGION', $cato);
        return response()->json([
            'success' => true,
            'status' => 201,
            'data' => $query->get()
        ]);   
   }

  ///Route:: /district_new/{cato}  || Method:: GET 
  public function ClientFields($cato)
  {
      $region = substr($cato, 0, 2);
      $district = substr($cato, 2, 4);
      if($district){
        $subquery = "AND CCI.DISTRICT = '$district' ";
      }
      else{
        $subquery = "";
      }
      $dbconn = DB::connection('CRM_DWH');
      $query =  $dbconn->select("SELECT  'clientLand' as type, CCR.[ID] as id
          ,CASE 
            	WHEN CCIG.GUID IS NULL THEN ''
            	WHEN CCIG.GUID IS NOT NULL THEN '1'
            END as guid
          ,CCI.NAME as name
          ,[CLIENT_INFO_ID] as client_info_id
          ,CCR.[COORDINATES] as  geometry_rings
      FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
      LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
      LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
      WHERE CCI.REGION = '$region' $subquery AND SOURCE=1
      ");    
      if (empty($query)) {
          return response()->json([
              'success' => false,
              'status' => 201,
              'region' => $region,
              'district' => $district
          ]);
      } else {
          return response()->json([
              'success' => true,
              'status' => 201,
              'region' => $region,
              'district' => $district,
              'data' => $query
            ]);
      }
  }
  
  //Route:: /elevatorMarker/ || Method:: GET
  public function ElevatorMarker(){
    $dbconn = DB::connection('CRM_DWH');
    $query =  $dbconn->select("SELECT ID, 
    NAME, 
    BIN, 
    LOCATION, 
    STATION, CONTACTS, STORAGE_VOLUME, LATITUDE, LONGITUDE
    FROM CRM_DWH.dbo.CRM_ELEVATOR WHERE LATITUDE IS NOT NULL AND STATUS = 'ДА'");
    return response()->json([
        'success' => true,
        'status' => 201,
        'data' => $query
    ]);
}

  //Route:: /district/{cato} || Method:: GET
  public function PolygonCLient($cato)
  {
      $dbconn = DB::congenection('CRM_DWH');
      $query =  $dbconn->select("SELECT  'clientPolygons' as type,
          CCR.[ID] as id
          ,CASE
              WHEN CCIG.GUID IS NOT NULL THEN '1'
          ELSE NULL
          END as guid
          ,CCI.NAME as name
          ,[CLIENT_INFO_ID] as client_info_id
          ,CCR.[COORDINATES] as  geometry_rings
      FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
      LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
      LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
      WHERE CCI.CATO LIKE '$cato%' 
      ");
    
      if (empty($query)) {
          return response()->json([
              'success' => false,
              'status' => 201
          ]);
      } else {
          return response()->json([
              'success' => true,
              'status' => 201,
              'data' => $query
          ]);
      }
  }

    //Route:: /clientDistrictFields/ || Method:: POST
    public function clientDistrictFields(Request $request)
    {
        $cato = $request->cato;
        $season = $request->season;
        $cult = $request->cult;
        if(count($cult)==2){
            $subquery2 = "AND CCR.CULTURE IN (20, 39)";
        }
        if(count($cult)==1){
            $subquery2 = "AND CCR.CULTURE IN ('20')";
        }
        else{
            $subquery2 = '';
        }
        $dbconn = DB::connection('CRM_DWH');
        $query =  $dbconn->select("SELECT  'clientLand' as type, CCR.[ID] as id
            ,CONVERT(NVARCHAR(max), CCIG.GUID, 1) guid
            ,CCI.NAME as name
            ,[CLIENT_INFO_ID] as client_info_id
            ,CCR.[COORDINATES] as  geometry_rings
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
        LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
        LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
        WHERE CCI.REGION = '$cato' AND SOURCE=1");
     
        $query_2 = $dbconn->select("
        SELECT SUM(AREA)/10000 as area_g, 
        COUNT(*) as count_fields
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
        LEFT JOIN CRM_CLIENT_INFO cci ON cci.ID = CCR.CLIENT_INFO_ID 
        WHERE cci.REGION = '$cato'  $subquery2 AND SOURCE=1; 
        ");
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'status' => 201
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => 201,
                'headers' => $query_2[0],
                'data' => $query
            ]);
        }
    }

    //Route:: /getCultureSpr || Method:: GET
    public function GetCultureSpr($cato){
        $dbconn = DB::connection('CRM_DWH');
        $region = substr($cato, 0, 2);
        if($region){
            $sql = "cci.REGION = $region";
        }
        $district = substr($cato, 2, 4);
        if($district){
            $sql2="and cci.DISTRICT = $district";
        }
        else{
            $sql2="";
        }
        $query = $dbconn->select("SELECT CSC.ID, CSC.NAME  FROM [CRM_DWH].[dbo].[CRM_SPR_CULTURE] CSC
        LEFT JOIN CRM_DWH.dbo.CRM_CLIENT_PROPERTIES ccp ON ccp.CULTURE = CSC.ID 
        LEFT JOIN CRM_DWH.dbo.CRM_CLIENT_INFO cci ON cci.ID = ccp.CLIENT_INFO_ID
        WHERE $sql $sql2 GROUP BY CSC.ID, CSC.NAME");
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201,
                'message' => 'Not data'
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }

    //Route:: /filterFields || Method:: POST
    public function FilterClientFields(Request $request)
    {   
        $iin = $request->iin;
        $cato = $request->cato;
        $cultArr = $request->cult;
        $areaf = $request->areaFrom;
        $areaupto =  $request->areaUpTo;
        $clientAA = $request->clientAA;

        if(count($cultArr)>0 && !empty($iin)){
            $cult = implode(",", $cultArr);
            $condition = "AND CCI.IIN_BIN = '$iin' AND CCR.CULTURE in ($cult) AND CCR.SOURCE = 1";
        }
        elseif(!empty($iin)) {
            $condition = "AND CCI.IIN_BIN = '$iin' AND CCR.SOURCE = 1";
        }
        elseif(count($cultArr)>0){
           $cult = implode(",", $cultArr);
           $condition = "AND CCR.CULTURE in ($cult) AND CCR.SOURCE = 1";
        }
        else{
            $condition = "AND CCR.SOURCE= 1";        
        }
        $dbconn = DB::connection('CRM_DWH');
        $query =  $dbconn->select("
            SELECT 
                'clientPolygons' as type
                ,CCR.[ID] as id
                ,CONVERT(NVARCHAR(max), CCIG.GUID, 1) guid
                ,CCI.NAME as name
                ,[CLIENT_INFO_ID] as client_info_id
                ,CCR.CULTURE as cultureId
                ,CSC.NAME
                ,CCR.[COORDINATES] as  geometry_rings
            FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_SPR_CULTURE] AS CSC ON CSC.ID=CCR.CULTURE
                WHERE CCI.CATO LIKE '$cato%' $condition
            ORDER BY CULTURE ASC
            ");
     
        $query_2 = $dbconn->select("
            SELECT SUM(AREA)/10000 as area_g, 
                COUNT(*) as count_fields
                FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN CRM_CLIENT_INFO CCI ON CCI.ID = CCR.CLIENT_INFO_ID
            WHERE CCI.CATO LIKE '$cato%' $condition; 
        ");
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'status' => 201
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => 201,
                'headers' => $query_2[0],
                'data' => $query
            ]);
        }
    }

   
    public function District($kat_f)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 'district' as type, [ID] as id
        ,[TEXT] as name
        ,[KLKOD] as klkod
        ,[VNAIM] as vnaim
        ,SUBSTRING([KATO], 0, 5) as cato
        ,[geometry_rings]
        FROM [CRM_DWH].[dbo].[CRM_AISGZK_RAION_GEO]
        WHERE KATO LIKE '$kat_f%'
        ");

        $query_2 = $dbconn->select("
        SELECT SUM(AREA)/10000 as AREA_G, COUNT(*) as COUNT_FIELDS
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
        LEFT JOIN CRM_CLIENT_INFO cci ON cci.CLIENT_ID = CCR.CLIENT_INFO_ID
        WHERE cci.CATO LIKE '$kat_f%' AND CCR.SOURCE = 1;
        "); 

        return response()->json([
            'success' => true,
            'headers' => $query_2[0],
            'data' => $query
            
        ]);
    }


    //
    public function PolygonDetail($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT CCR.[ID] as id
        ,[FIELDS] as fields
        ,[CLIENT_INFO_ID] as client_info_id
        ,CCI.NAME as name
        ,CCI.ADDRESS as address
        ,CCI.CATO as cato
        ,CCR.COORDINATES as geometry_rings
        ,[TYPE] as type
        ,[KADASTR] as kadastr
        ,[AREA] as area
    FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
    LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
    LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
    WHERE CCR.[ID] = '$id'
    ");
        return response()->json([
            'success' => true,
            'status' => 201,
            'data' => $query
        ]);
    }
    //old version
    public function FieldsDetail($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 'clientFieldsAll' as type 
            ,CCR.[ID] as id 
            ,[FIELDS] as fields
            ,[CLIENT_INFO_ID] as client_info_id
            ,CASE 
            	WHEN CCIG.GUID IS NULL THEN NULL
            	WHEN CCIG.GUID IS NOT NULL THEN 1
            END as guid
            ,CCR.COORDINATES as geometry_rings
            ,[AREA]/10000 as area
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
            WHERE CLIENT_INFO_ID = $id
    ");

        $arr = json_decode($query[0]->geometry_rings, true);

        return response()->json([
            'success' => true,
            'status' => 201,
            'geo' => $arr,
            'data' => $query
        ]);
    }


    //Route:: /clientFieldsCult/ || Method:: GET
    public function ClientGroupCulture(Request $request)
    {    
        $dbconn = DB::connection('CRM_DWH');
        if($request->type == "allFields"){
            $query = $dbconn->select("
            SELECT 'clientLand' as type 
            ,CCR.[ID] as id 
            ,[FIELDS] as fields
            ,[CLIENT_INFO_ID] as client_info_id
            ,CASE 
                WHEN CCIG.GUID IS NULL THEN NULL
                WHEN CCIG.GUID IS NOT NULL THEN 1
            END as guid
            ,CCR.COORDINATES as geometry_rings
            ,[AREA]/10000 as area
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
            WHERE CLIENT_INFO_ID = '$request->id'
            ");
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
        if($request->type == "cultures"){
            $query = $dbconn->select("
            SELECT 'clientLandCulture' as type,
                CSC.NAME as nameCult,
                CULTURE as fieldsCultureId,
                CCR.CLIENT_INFO_ID as client_info_id,
                CCR.ID as fieldsID,
                CSC.COLOR as color,
                CCR.CLIENT_INFO_ID as client_info_id,
                CCR.COORDINATES as geometry_rings
            FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
                LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
                LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
                LEFT JOIN [CRM_DWH].[dbo].[CRM_SPR_CULTURE] AS CSC ON CSC.ID = CCR.CULTURE
                WHERE CLIENT_INFO_ID = $request->id AND SOURCE=1
            ");
            $query_2 = $dbconn->select("
            SELECT SUM(AREA)/10000 as area_g, 
                COUNT(*) as count_fields,
                cci.NAME as name,
                cci.ADDRESS as address
            FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN CRM_CLIENT_INFO cci ON cci.ID = CCR.CLIENT_INFO_ID  
                WHERE CCR.CLIENT_INFO_ID  = '$request->id' AND SOURCE=1 GROUP BY cci.NAME,
                cci.ADDRESS");
            
            return response()->json([
                    'success' => true,
                    'status' => 201,
                    'headers'=> $query_2[0],
                    'data' => $query
                    ///'data' => ClientsFieldsPolygonResource::collection($query)
                ]);
        }
       else{
        return response()->json([
                'success' => false,
                'status' => 201
            ]);
       }
    }

    //Route:: /getClientFieldsCult/ || Method:: POST
    public function ClientFieldGuid(Request $request)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 'clientLandPlot' as type 
            ,CCR.[ID] as id 
            ,[FIELDS] as fields
            ,[CLIENT_INFO_ID] as client_info_id
            ,CONVERT(NVARCHAR(max), CCIG.GUID, 1) guid
            ,CASE 
                WHEN CCIG.GUID IS NOT NULL THEN '1'
                WHEN CCIG.GUID IS NULL THEN NULL
            END AS guid
            ,CCR.COORDINATES as geometry_rings
            ,[CULTURE] as culture
            ,CONCAT([AREA]/10000,  ' Га') as area 
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
            WHERE CLIENT_INFO_ID = '$request->clientField' AND CCR.CULTURE = $request->fieldsCultureId AND SOURCE=1
        ");
        $query_2 = $dbconn->select("
        SELECT SUM(AREA)/10000 as area_g, 
            COUNT(*) as count_fields
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
        LEFT JOIN CRM_CLIENT_INFO cci ON cci.ID = CCR.CLIENT_INFO_ID  
            WHERE CLIENT_INFO_ID = '$request->clientField' AND CCR.CULTURE = $request->fieldsCultureId  AND SOURCE=1;");
        return response()->json([
            'success' => true,
            'status' => 201,
            'headers' => $query_2[0],
            'data' => $query
        ]);
    }


//{ TEST API  }
    public function todol(Request $request){
        // $login = $request->login;
        // $password = $request->password;
        // $ldapconn = ldap_connect("192.168.1.241")
        // or die("Could not connect to LDAP server.");
        // if ($ldapconn) {
        //     // binding to ldap server
        //     $ldapbind = ldap_bind($ldapconn, "d.onglassyn@alagro.local", "QWE123qwe!");
        //     if ($ldapbind) {
        //         return response()->json([
        //             "succes"=> true,
        //             "status" => 201,
        //             "message" => "LDAP-привязка успешна..."
        //         ]);
        //     } 
        //     else {
        //         return response()->json([
        //             "succes"=> true,
        //             "status" => 201,
        //             "message" => "LDAP-привязка не успешна..."
        //         ]);
        //     }

        // }
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 'clientFieldCulture' as type,
            CSC.NAME as nameCult,
            CULTURE as fieldsCultureId,
            CCR.CLIENT_INFO_ID as client_info_id,
            CSC.COLOR as color,
            CCR.CLIENT_INFO_ID as client_info_id
            ,geometry_rings=(SELECT STRING_AGG(COORDINATES, ' || ')  FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] CCP WHERE CCP.CLIENT_INFO_ID =CCR.CLIENT_INFO_ID AND CCP.CULTURE =CCR.CULTURE)
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI ON CCI.ID=CCR.CLIENT_INFO_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON CCIG.ID=CCI.CLIENT_ID
            LEFT JOIN [CRM_DWH].[dbo].[CRM_SPR_CULTURE] AS CSC ON CSC.ID = CCR.CULTURE
            WHERE CLIENT_INFO_ID = $request->id AND SOURCE=1
        GROUP BY 
            CULTURE,
            CSC.NAME,
            CSC.COLOR,
            CCR.CLIENT_INFO_ID
        ");
        $query_2 = $dbconn->select("
        SELECT SUM(AREA)/10000 as area_g, 
            COUNT(*) as count_fields,
            cci.NAME as name,
            cci.ADDRESS as address
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCR
        LEFT JOIN CRM_CLIENT_INFO cci ON cci.ID = CCR.CLIENT_INFO_ID  
            WHERE CCR.CLIENT_INFO_ID  = '$request->id' AND SOURCE=1 GROUP BY cci.NAME,
            cci.ADDRESS");
        
        $nnarray = [];

        foreach($query as $geom){
            $array_geo = explode(" || ", $geom->geometry_rings);
            $serialize = json_decode($array_geo[0]);
            $data = array(
                "type" => "clientFieldCulture", 
                "nameCult" => $geom->nameCult, 
                "fieldsCultureId" =>$geom->fieldsCultureId,
                "client_info_id" => $geom->client_info_id,
                "color" => $geom->color,
                "geometry_rings" => $serialize
            );
            array_push($nnarray, $data);
        }
        return response()->json([
            'success' => true,
            'status' => 201,
            'headers' => $query_2[0],
            'data' => $nnarray
        ]);

    }
    public function TestClientFields(Request $request) {
        $request->year;
        $request->provider;
        $request->region;

        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT 
        csc.NAME,
        CSS.PROVIDER_NAME,
        CSS.REGION,
            SUM(SUM_SUBSIDIES) as SUM_SUBSID,
            SUM(AREA) as AREA_SUBS,
            SUM(CSS.VOLUME) as VOLUME,
            CSS.UNIT,
            SUM(ROUND(USAGE_AREA, 0)) as USAGE_AREA
        FROM CRM_SHYMBULAK_SEEDS CSS
        LEFT JOIN CRM_CLIENT_INFO cci ON cci.IIN_BIN = CSS.APPLICANT_IIN_BIN
        LEFT JOIN CRM_CLIENT_PROPERTIES ccp ON ccp.CLIENT_INFO_ID = cci.ID 
        LEFT JOIN CRM_SPR_CULTURE csc on csc.ID = ccp.CULTURE 
        WHERE CSS.[YEAR] IN ('$request->year') AND [SOURCE] = 1 
        group by csc.NAME,PROVIDER_NAME,CSS.UNIT, CSS.REGION
        order by SUM_SUBSID DESC");
        return Response($query);
    }
    ///{ TEST API }



    ///api for search client name for maps
    public function FindClientName($iin)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT CCP.[ID]
            ,CONVERT(NVARCHAR(max), CCIG.GUID, 1) as GUID
            ,[CLIENT_ID]
            ,[NAME]
            ,[IIN_BIN]
    FROM [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI
    LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCP ON CCP.CLIENT_INFO_ID=CCI.ID
    LEFT JOIN [dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON  CCIG.ID=CCI.CLIENT_ID
    WHERE IIN_BIN like '$iin%' AND CCP.COORDINATES!=''");
        return response($query);
    }
    //end api for search 

///api for agri sopr *агросопровождение*
    //------------ Создание записи по разделу агросопровождению
    //создать запись от менеджера по клиентскому полю
    public function CreateRecordMngFields(Request $request){
        $dbconn = DB::connection("CRM_DWH");
        $query = $dbconn->table("")
        ->select("")
        ->insert([]);
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
        
    }
    //изменить запись менеджера по агросопровождению 
    public function UpdateRecordMngFields(Request $request){
        $dbconn = DB::connection("CRM_DWH");
        $query = $dbconn->table("")
        ->select("")
        ->update([]);
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
        
    }
    //удалить запись менеджера по агросопровождению
    public function DeleteRecordMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("")
        ->delete("");
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //показать все запись по агросопровождению
    public function ViewRecordMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("");
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //показать детально одну запись
    public function ViewDetailRecordMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("");
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //------------ Справочник услуг по агросопровождению 
    //Отобразить Справочник услуг по агросопровождению
    public function ViewSprServiceMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("");
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //Добавить запись в справочник 
    public function AddRecordSprServiceMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("")
        ->insert([]);
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //Изменить запись в справочнике 
    public function UpdateRecordSprServiceMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("")
        ->update([]);
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }
    //Удалить запись в справочнике 
    public function DeleteRecordSprServiceMngFields(Request $request) {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->table("")
        ->select("")
        ->where("")
        ->delete();
        if(empty($query)){
            return response()->json([
                'succes' => false,
                'status' => 201
            ]);
        }
        else{
            return response()->json([
                'succes' => true,
                'status' => 201,
                'data' => $query
            ]);
        }
    }

///end api agroServie *агросопровождение


/// api for client data 
    ///api for view client list 
    public function Client_list($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT [NAME]
            ,[ADDRESS]
            ,[CLIENT_ID]
            ,[IIN_BIN]
            ,CATO
            ,CASE
                WHEN GUID!='' THEN 'Постоянный клиент'
                ELSE 'Новый клиент'
            END AS CLIENT_CHECK,
        COUNT_FIELDS = (SELECT count(ccp.ID)  FROM CRM_CLIENT_PROPERTIES ccp WHERE ccp.CLIENT_INFO_ID = CCI.CLIENT_ID)
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI
        LEFT JOIN [dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON  CCIG.ID=CCI.CLIENT_ID
        WHERE REGION = '$id'
        GROUP BY GUID
            ,[NAME]
            ,[ADDRESS]
            ,[CLIENT_ID]
            ,[IIN_BIN]
            ,CATO
        ORDER BY COUNT_FIELDS DESC
        ");
        return response($query);
    }



    ///api for client info detail
    public function ClientInfo($guid)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT CONVERT(NVARCHAR(max), CCIG.GUID, 1) as GUID
            ,CCI.NAME
            ,[ADDRESS]
            ,CCI.[CLIENT_ID]
            ,CCI.ID
            ,[IIN_BIN]
            ,CATO
            ,CCI.DEYATELNOST AS ACTIVITY
            ,CCC.NAME AS NAMECONTACT
            ,CCC.POSITION
            ,CCC.PHONE_NUMBER
            ,CCC.EMAIL
    FROM [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI
    LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCP ON CCP.CLIENT_INFO_ID=CCI.ID
    LEFT JOIN [dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON  CCIG.ID=CCI.CLIENT_ID
    LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_CONTACTS] AS CCC ON CCC.CLIENT_ID=CCIG.ID
        WHERE CCI.CLIENT_ID=$guid 
        GROUP BY GUID
            ,CCI.NAME  
            ,[ADDRESS]
            ,CCI.DEYATELNOST
            ,CCI.[CLIENT_ID]
            ,CCI.ID
            ,[IIN_BIN]
            ,CATO
            ,CCC.NAME
            ,CCC.POSITION
            ,CCC.PHONE_NUMBER
            ,CCC.EMAIL");

        $query2 = $dbconn->select("SELECT 
            COUNT(*) as COUNT_FIELDS, 
            SUM(AREA) as AREA  
        FROM [CRM_DWH].[dbo].[CRM_CLIENT_INFO] AS CCI
        LEFT JOIN [CRM_DWH].[dbo].[CRM_CLIENT_PROPERTIES] AS CCP ON CCP.CLIENT_INFO_ID=CCI.ID
        LEFT JOIN [dbo].[CRM_CLIENT_ID_GUID] AS CCIG ON  CCIG.ID=CCI.CLIENT_ID
        WHERE CCI.CLIENT_ID = $guid and CCIG.GUID is NOT NULL ");
        return response()->json([
            'data' => $query[0],
            'FIELDS' => $query2[0]->COUNT_FIELDS,
            'AREA' => $query2[0]->AREA
        ]);
    }


// api for link client manager
    public function ManagerClientLink($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("SELECT TOP 1 CIG.ID, CONVERT(NVARCHAR(MAX), CD.GUID, 1) as GUID, 
            CD.NAIMENOVANIE, 
            CD.DATA_NACHALA_DEYSTVIYA, 
            CD.DATA_OKONCHANIYA_DEYSTVIYA, 
            CD.NOMER, 
            CD.STATUS, 
            CD.KONTRAGENT,
            CD.MENEDZHER AS manager, 
            CONVERT(NVARCHAR(MAX), CD.MENEDZHER_GUID, 1) AS MENEDZHER_GUID,
            CD.SEZON,
            CD.ADRES_DOSTAVKI,
            CD.SUMMA_KZ_TG
        FROM CRM_DWH.dbo.CRM_CLIENT_ID_GUID CIG
        INNER JOIN CRM_DWH.dbo.CRM_DOGOVOR CD ON CD.KONTRAGENT_GUID = CIG.GUID
        WHERE CIG.ID = $id 
        ORDER BY DATA_NACHALA_DEYSTVIYA DESC
        ");
        $query2 = $dbconn->select("
        SELECT COUNT(*) as COUNT_CONTRACT
        FROM CRM_DWH.dbo.CRM_CLIENT_ID_GUID CIG
        INNER JOIN CRM_DWH.dbo.CRM_DOGOVOR CD ON CD.KONTRAGENT_GUID = CIG.GUID
        WHERE CIG.ID = $id
        ");
        $query2 = $dbconn->select("
        SELECT COUNT(*) as COUNT_CONTRACT
        FROM CRM_DWH.dbo.CRM_CLIENT_ID_GUID CIG
        INNER JOIN CRM_DWH.dbo.CRM_DOGOVOR CD ON CD.KONTRAGENT_GUID = CIG.GUID
        WHERE CIG.ID = $id
        ");
        $query3 = $dbconn->select("
        SELECT 
            CU.ID AS MANAGER_ID,
            CU.NAIMENOVANIE AS MANAGER_NAME,
            CU.DIREKTSYA,
            CU.DOLZHNOST,
            SEZON
            FROM CRM_CLIENT_ID_GUID ccig 
            LEFT JOIN CRM_DOGOVOR cd ON cd.KONTRAGENT_GUID = ccig.GUID
            LEFT JOIN CRM_USERS CU ON CU.GUID = CD.MENEDZHER_GUID
            WHERE CCIG.ID = $id 
            group by 
            CU.ID,
            CU.NAIMENOVANIE,
            CU.DIREKTSYA,
            CU.DOLZHNOST,
            SEZON
            order by sezon desc
        ");
        if ($query) {
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $query,
                'count' => $query2,
                'data_chrono' => $query3
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 201,
            ]);
        }
    }



// order manager 
    public function AllOrdersClient($client_id){
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 
        CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID,
        CU.ID AS MANAGER_ID,
        CU.NAIMENOVANIE AS MANAGER_NAME,
        CU.DIREKTSYA,
        CU.DOLZHNOST,
        CCIG.ID AS KONTRAGENT_ID, 
        CD.KONTRAGENT AS KONTRAGENT_NAME,
        CD.NAIMENOVANIE,
        CCI.IIN_BIN AS KONTRAGENT_IIN,
        SEZON AS SEASON,
        CD.USLOVIYA_OPLATY,
        CD.SPOSOB_DOSTAVKI,
        CD.ADRES_DOSTAVKI,
        CD.SUMMA_KZ_TG
    FROM CRM_CLIENT_ID_GUID ccig 
    LEFT JOIN CRM_DOGOVOR cd ON cd.KONTRAGENT_GUID = ccig.GUID
    LEFT JOIN CRM_USERS CU ON CU.GUID = CD.MENEDZHER_GUID
    LEFT JOIN CRM_CLIENT_INFO CCI ON CCI.CLIENT_ID = ccig.ID
    WHERE CCI.CLIENT_ID = $client_id  AND cd.OSNOVNOY_DOGOVOR='' AND SEZON IN ('Сезон 2022', 'Сезон 2021')
        ");
        return response($query);
    }

    public function ListOrders($user_id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 
            CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID,
            CU.ID AS MANAGER_ID,
            CU.NAIMENOVANIE AS MANAGER_NAME,
            CU.DIREKTSYA,
            CU.DOLZHNOST,
            CCIG.ID AS KONTRAGENT_ID, 
            CD.KONTRAGENT AS KONTRAGENT_NAME,
            CD.NAIMENOVANIE,
            CCI.IIN_BIN AS KONTRAGENT_IIN,
            SEZON AS SEASON,
            CD.USLOVIYA_OPLATY,
            CD.SPOSOB_DOSTAVKI,
            CD.ADRES_DOSTAVKI,
            CD.SUMMA_KZ_TG
        FROM CRM_CLIENT_ID_GUID ccig 
        LEFT JOIN CRM_DOGOVOR cd ON cd.KONTRAGENT_GUID = ccig.GUID
        LEFT JOIN CRM_USERS CU ON CU.GUID = CD.MENEDZHER_GUID
        LEFT JOIN CRM_CLIENT_INFO CCI ON CCI.CLIENT_ID = ccig.ID
        WHERE CU.ID = $user_id  AND cd.OSNOVNOY_DOGOVOR='' AND SEZON IN ('Сезон 2022', 'Сезон 2021')
        ");
        if ($query) {
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $query
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 201,
                'data' => 'Data not found'
            ]);
        }
    }


//api for Detail contracts *Детальный просмотр договора
    public function DetailOrders($id)
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT 
        CONVERT(NVARCHAR(MAX), DOGOVOR_GUID, 1) AS CONTRACT_GUID, 
        CD.NAIMENOVANIE AS CONTRACT_NAME,
        PERIOD,
        CONVERT(NVARCHAR(MAX), NOMENKLATURA_GUID, 1) AS PRODUCT_GUID,
        NOMENKLATURA AS PRODUCT_NAME,
        VIDY_KULTUR AS TYPE_CULTURE,
        KOLICHESTVO  AS COUNT_PRODUCT,
        TSENA AS PRICE_PRODUCT,
        TSENA_SO_SKIDKOY AS PRICE_DISCOUNT,
        TSENA_PO_PRAYS_LISTU AS PRICE_CATALOG,
        TSENA_MIN  AS PRICE_MIN,
        SUMMA AS SUM_PRICE,
        SUMMA_SO_SKIDKOY AS SUM_DISCOUNT,
        CONVERT(NVARCHAR(MAX), SKLAD_OTGRUZKI_GUID, 1) AS WAREHOUSE_GUID,
        SKLAD_OTGRUZKI AS WAREHOUS,
        CDS.SUMMA_KZ_TG AS SUM_TENGE
        FROM CRM_DWH.dbo.CRM_DOGOVOR_SPETSIFIKATSIYA CDS
        LEFT JOIN CRM_DOGOVOR CD ON CD.GUID = CDS.DOGOVOR_GUID
        WHERE DOGOVOR_GUID=$id
        ");

        $query2 = $dbconn->select("
        SELECT 
            CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS CONTRACTS_GUID,
            CU.ID AS MANAGER_ID,
            CU.NAIMENOVANIE AS MANAGER_NAME,
            CU.DIREKTSYA,
            CU.DOLZHNOST,
            CCIG.ID AS KONTRAGENT_ID, 
            CD.KONTRAGENT AS KONTRAGENT_NAME,
            CD.NAIMENOVANIE,
            SEZON AS SEASON,
            CD.USLOVIYA_OPLATY,
            CD.SPOSOB_DOSTAVKI,
            CD.ADRES_DOSTAVKI,
            CD.SUMMA_KZ_TG,
            CD.NOMER_DOP_SOGLASHENIYA,
            CONVERT(NVARCHAR(MAX), CD.OSNOVNOY_DOGOVOR, 1) AS MAIN_CONTRACTS
        FROM CRM_CLIENT_ID_GUID ccig 
        LEFT JOIN CRM_DOGOVOR cd ON cd.KONTRAGENT_GUID = ccig.GUID
        LEFT JOIN CRM_USERS CU ON CU.GUID = CD.MENEDZHER_GUID
        WHERE CD.GUID = $id
        ");
        $mainc = $query2[0]->CONTRACTS_GUID;

        $query3 = $dbconn->select("
        SELECT CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS GUID, CD.NAIMENOVANIE,
        CD.NAIMENOVANIE, CONVERT(NVARCHAR(MAX), OSNOVNOY_DOGOVOR, 1) as OSN
        FROM CRM_DOGOVOR CD
            WHERE CD.OSNOVNOY_DOGOVOR = $mainc
        ");
        if ($query) {
            return response()->json([
                'succes' => false,
                'status' => 201,
                'data' => $query,
                'data_contracts' => $query2,
                'addiconal_contracts' => $query3
            ]);
        } else {
            return response()->json([
                'succes' => false,
                'status' => 201,
                'data' => 'Data not found'
            ]);
        }
    }

// api for Link Adicional Contracts *Cвязка клиента с менеджером
    public function LinkAdicionalOrder($id)
    {
        $dbconn = DB::connectioon('CRM_DWH');
        $query = $dbconn->select("
        SELECT CONVERT(NVARCHAR(MAX), CD.GUID, 1) AS GUID, CD.NAIMENOVANIE, CD.KONTRAGENT AS KONTRAGENT_NAME,
        CD.NAIMENOVANIE,
        SEZON,
        CD.USLOVIYA_OPLATY,
        CD.SPOSOB_DOSTAVKI,
        CD.ADRES_DOSTAVKI
        FROM CRM_DOGOVOR CD
            WHERE GUID = $id
        ");
        if ($query) {
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $query
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 201,
                'data' => 'not data'
            ]);
        }
    }

/// для инсенрта всех пользователей из таблицы users в таблицу crm_users
    public function Migrationalluser()
    {
        $dbconn = DB::connection('CRM_DWH');
        $query = $dbconn->select("
        SELECT NAIMENOVANIE, ADRES_E_P
        FROM CRM_DWH.dbo.CRM_USERS 
        WHERE ADRES_E_P LIKE '%@alemagro%' 
        ");
        foreach ($query as $t) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => '192.168.1.16/api/auth/register',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'email=' . $t->ADRES_E_P . '&password=' . $t->ADRES_E_P . '&name=' . $t->NAIMENOVANIE . '',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        }
    }
}
