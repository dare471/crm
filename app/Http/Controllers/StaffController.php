<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use App\Http\Resources\ClientsFieldsPolygonResource;
use App\Http\Resources\MongoExample;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TopProductManager;
use App\Http\Resources\UserSPR;

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
            WHERE [TELEGRAM_ID]!=0 AND[NAIMENOVANIE]!='telegrambot' AND CH.[DOLZHNOST]='Менеджер по продажам' AND [SOSTOYANIE]!='Увольнение'  ORDER BY [NAIMENOVANIE] ASC");
        return response($query);
    }

    public function UserSPRTable($id)
    {
        $query=DB::table("CRM_USERS as cu")
        ->leftjoin("users as u", "u.email", "cu.ADRES_E_P")
        ->select(DB::raw("CONVERT(NVARCHAR(MAX), cu.GUID, 1) as guid"), "cu.ID", 
        "cu.TELEGRAM_ID", "NAIMENOVANIE", "DIREKTSYA", "DOLZHNOST", "ADRES_E_P", "PODRAZDELENIE", "CRM_CATO")
        ->where("u.id", "$id")
        ->get();
        return UserSPR::collection($query)->all();
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
        WHERE [ID]=$id ORDER BY [NAIMENOVANIE] ASC");
        return response()->json([
            'user_inf' => $user_inf,
        ]);
    }


    ///Maps api start 
    //Route:: /country || Method:: GET
    public function Region(){
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

public function District($kat_f){
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


    public function ManagerAnalyse(Request $request){
        if($request->type=="manager"){
            $query = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient, SEZON as season"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.ID", $request->userId)
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('Сезон 2021', 'Сезон 2020')")])
            ->groupBy("SEZON")
            ->get();

            $query2 = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.ID", $request->userId)
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('Сезон 2021', 'Сезон 2020')")])
            ->get();
            
            $querySumOldClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.ID", $request->userId)
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $querySumNewClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.ID", $request->userId)
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $queryTP = DB::table("CRM_DOGOVOR as CD")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->leftjoin("CRM_DOGOVOR_SPETSIFIKATSIYA as CDS", "CDS.DOGOVOR_GUID", "CD.GUID")
            ->select("NOMENKLATURA as productName", DB::raw("Cast(SUM(KOLICHESTVO) as integer) as productCount"))
            ->where("SEZON", "Сезон 2022")
            ->where("cu.ID", $request->userId)
            ->groupBy("NOMENKLATURA")
            ->orderByDesc("productCount")
            ->limit("10")
            ->get();

            $proccentSumContract = $querySumNewClient[0]->cahsSum / ($querySumNewClient[0]->cahsSum + $querySumOldClient[0]->cahsSum) * 100;
            $proccentNewClient =  $query2[0]->countClient / ($query2[0]->countClient + $query[0]->countClient) * 100; 

            return response()->json([
                "success"=> true,
                "status"=> 201,
                "data" => [
                    "type" => "managerAnalyse",
                    "compareSeason" => "Сезон 2021",
                    "currentSeason" => $query[0]->season,
                    "clientAnalyse" => [
                        "proccentProfitClient" => (int)$proccentNewClient,
                        "newCountClient" => (int)$query2[0]->countClient,
                        "oldCountClient" => (int)$query[0]->countClient,
                    ],
                    "contractAnalyse" => [
                        "proccentProfitContract" => (int)$proccentSumContract,
                        "newSumContract" => (int)$querySumNewClient[0]->cahsSum,
                        "oldSumContract" => (int)$querySumOldClient[0]->cahsSum,
                    ],
                    "topProduct10" => TopProductManager::collection($queryTP)
                ]
            ]);
        }
        if($request->type == "region"){
            $query = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient, SEZON as season"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.CRM_CATO", $request->regionId)
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->groupBy("SEZON")
            ->get();

            $query2 = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.CRM_CATO", $request->regionId)
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();
            
            $querySumOldClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.CRM_CATO", $request->regionId)
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $querySumNewClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->where("CU.CRM_CATO", $request->regionId)
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $queryTP = DB::table("CRM_DOGOVOR as CD")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->leftjoin("CRM_DOGOVOR_SPETSIFIKATSIYA as CDS", "CDS.DOGOVOR_GUID", "CD.GUID")
            ->select("NOMENKLATURA as productName", DB::raw("Cast(SUM(KOLICHESTVO) as integer) as productCount"))
            ->where("SEZON", "Сезон 2022")
            ->where("cu.CRM_CATO", $request->regionId)
            ->groupBy("NOMENKLATURA")
            ->orderByDesc("productCount")
            ->limit("10")
            ->get();

            $proccentSumContract = $querySumNewClient[0]->cahsSum / $querySumOldClient[0]->cahsSum * 100 - 100;
            $proccentNewClient = $query2[0]->countClient / $query[0]->countClient * 100 - 100; 

            return response()->json([
                "success"=> true,
                "status"=> 201,
                "data" => [
                    "type" => "regionAnalyse",
                    "compareSeason" => "Сезон 2021",
                    "currentSeason" => $query[0]->season,
                    "clientAnalyse" => [
                        "proccentProfitClient" => (int)$proccentNewClient,
                        "newCountClient" => (int)$query2[0]->countClient,
                        "oldCountClient" => (int)$query[0]->countClient,
                    ],
                    "contractAnalyse" => [
                        "proccentProfitContract" => (int)$proccentSumContract,
                        "newSumContract" => (int)$querySumNewClient[0]->cahsSum,
                        "oldSumContract" => (int)$querySumOldClient[0]->cahsSum,
                    ],
                    "topProduct10" => TopProductManager::collection($queryTP)
                ]
            ]);
        }
        if($request->type == "country"){
            $query = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient, SEZON as season"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->groupBy("SEZON")
            ->get();

            $query2 = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("COUNT(*) as countClient"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();
            
            $querySumOldClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->whereNotIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $querySumNewClient = DB::table("CRM_DOGOVOR as CD")
            ->select(DB::raw("SUM(CD.SUMMA_KZ_TG) as cahsSum"))
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->where("SEZON", "Сезон 2022")
            ->whereIn("KONTRAGENT_GUID", [DB::raw("SELECT CD1.KONTRAGENT_GUID FROM CRM_DOGOVOR CD1 WHERE MENEDZHER_GUID = CD.MENEDZHER_GUID AND SEZON IN ('$request->compareSeason')")])
            ->get();

            $queryTP = DB::table("CRM_DOGOVOR as CD")
            ->leftjoin("CRM_USERS as CU", "CU.GUID", "CD.MENEDZHER_GUID")
            ->leftjoin("CRM_DOGOVOR_SPETSIFIKATSIYA as CDS", "CDS.DOGOVOR_GUID", "CD.GUID")
            ->select("NOMENKLATURA as productName", DB::raw("Cast(SUM(KOLICHESTVO) as integer) as productCount"))
            ->where("SEZON", "Сезон 2022")
            ->groupBy("NOMENKLATURA")
            ->orderByDesc("productCount")
            ->limit("10")
            ->get();

            $proccentSumContract = $querySumNewClient[0]->cahsSum / $querySumOldClient[0]->cahsSum * 100 - 100;
            $proccentNewClient = $query2[0]->countClient / $query[0]->countClient * 100 - 100; 

            return response()->json([
                "success"=> true,
                "status"=> 201,
                "data" => [
                    "type" => "countryAnalyse",
                    "compareSeason" => "Сезон 2021",
                    "currentSeason" => $query[0]->season,
                    "clientAnalyse" => [
                        "proccentProfitClient" => (int)$proccentNewClient,
                        "newCountClient" => (int)$query2[0]->countClient,
                        "oldCountClient" => (int)$query[0]->countClient,
                    ],
                    "contractAnalyse" => [
                        "proccentProfitContract" => (int)$proccentSumContract,
                        "newSumContract" => (int)$querySumNewClient[0]->cahsSum,
                        "oldSumContract" => (int)$querySumOldClient[0]->cahsSum,
                    ],
                    "topProduct10" => TopProductManager::collection($queryTP)
                ]
            ]);
        }
    }
}
