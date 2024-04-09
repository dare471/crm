<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContestController extends Controller
{
    public function ControllerConditionContest(Request $request){
        $currentDate = Carbon::now();
        $pool1StartDate = Carbon::parse('2023-03-13'); // создать объект даты для начала интервала
        $pool1EndDate = Carbon::parse('2023-04-02'); // создать объект даты для конца интервала
        $pool2StartDate = Carbon::parse('2023-04-03');
        $pool2EndDate = Carbon::parse('2023-04-23');
        $pool3StartDate = Carbon::parse('2023-04-24');
        $pool3EndDate = Carbon::parse('2023-05-15');
        $pool4StartDate = Carbon::parse('2023-05-15');
        $pool4EndDate = Carbon::parse('2023-06-04');
        $pool5StartDate = Carbon::parse('2023-06-05');
        $pool5EndDate = Carbon::parse('2023-06-25');
        $pool6StartDate = Carbon::parse('2023-06-26');
        $pool6EndDate = Carbon::parse('2023-07-16');

        switch(true){
            case $currentDate->between($pool1StartDate, $pool1EndDate):
               return $this->MainContest("CRM_SALES_CONTEST_STAGE_1");
                break;
            case $currentDate->between($pool2StartDate, $pool2EndDate):
                return $this->MainContest("CRM_SALES_CONTEST_STAGE_2");
                break;
            case $currentDate->between($pool3StartDate, $pool3EndDate):
                $daysLeft = abs($currentDate->diffInDays($pool2EndDate, false));
                $daysLeft = $daysLeft * 5;
                return collect([
                    "AsiaAfrica" => ["progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_1")],
                    "AfricaEurope" => [ "progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_2")], 
                    "EuropeUsa" => [ "progress" => $daysLeft,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_3")],
                    "UsaAntartica" => [ "progress" => null, "data" => []],
                    "AntarticaAustralia" => [ "progress" => null, "data" => []],
                    "AustraliaAsia" => [ "progress" => null, "data" => []],
                ]);
                break;
            case $currentDate->between($pool4StartDate, $pool4EndDate):
                $daysLeft = abs($currentDate->diffInDays($pool3EndDate, false));
                $daysLeft = $daysLeft * 5;
                return collect([
                        "AsiaAfrica" => ["progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_1")],
                        "AfricaEurope" => [ "progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_2")], 
                        "EuropeUsa" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_3")],
                        "UsaAntartica" => [ "progress" => $daysLeft,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_4")],
                        "AntarticaAustralia" => [ "progress" => null, "data" => []],
                        "AustraliaAsia" => [ "progress" => null, "data" => []],
                ]);
                break;
            case $currentDate->between($pool5StartDate, $pool5EndDate):
                $daysLeft = abs($currentDate->diffInDays($pool4EndDate, false));
                $daysLeft = $daysLeft * 5;
                return collect([
                    "AsiaAfrica" => ["progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_1")],
                    "AfricaEurope" => [ "progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_2")], 
                    "EuropeUsa" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_3")],
                    "UsaAntartica" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_4")],
                    "AntarticaAustralia" => [ "progress" => $daysLeft,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_5")],
                    "AustraliaAsia" => [ "progress" => null, "data" => []],
                ]);
                break;
            case $currentDate->between($pool6StartDate, $pool6EndDate):
                    $daysLeft = abs($currentDate->diffInDays($pool5EndDate, false));
                    $daysLeft = $daysLeft * 5;
                    return collect([
                        "AsiaAfrica" => ["progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_1")],
                        "AfricaEurope" => [ "progress" => 100,  "data" => $this->MainContest("CRM_SALES_CONTEST_STAGE_2")], 
                        "EuropeUsa" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_3")],
                        "UsaAntartica" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_4")],
                        "AntarticaAustralia" => [ "progress" => 100,  "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_5")],
                        "AustraliaAsia" => [ "progress" => $daysLeft, "data" =>$this->MainContest("CRM_SALES_CONTEST_STAGE_6")],
                    ]);
                break;
            default:
                return collect([
                    "message"=> "get correct date"
                ]);
            break;    
        }
    }
    private function MainContest($table){
        $query = DB::table($table)
        ->limit(10)
        ->orderBy("RANK")
        ->get();
        return $query->map(function($item){
            return collect(
                [
                    "user" => " ".$item->MENEDZHER,
                    "sumMoney" => " ".number_format($item->SUMMA, '0', '.', ' ')." ₸",
                    "newClient" => " ".(int)$item->NEW_CLIENT,
                    "score" => " ".(int)$item->TOTAL,
                    "rank" =>  " ".(int)$item->RANK
                ]
            );
        })->all();
    }
    public function TableAll(Request $request){
        $query = DB::table('CRM_SALES_CONTEST_STAGE_6')
        ->orderBy("RANK")
        ->get();
        return $query->map(function($item){
            return collect(
                [
                    "user" => $item->MENEDZHER,
                    "sumMoney" => " ".number_format($item->SUMMA, '0', '.', ' ')." ₸",
                    "newClient" => (int)$item->NEW_CLIENT,
                    "score" => (int)$item->TOTAL,
                    "rank" =>  (int)$item->RANK
                ]
            );
        });
    }
    public function ProgressBar(Request $request){
        $query = DB::table("CRM_SALES_CONTEST_PROGRESS_BAR")
        ->get();    
        return $query->map(function($item){
            return collect([
                "title" => "<b>Новые Горизонты</b> <p>Текущее направление: <b>Австралия</b> -> <b>Азия</b></p><p> Процент выполнения конкурса: <b>100%</b></p>",
                "actualSum" => "5 000 000 000 ₸",
                "planSum"=> number_format( $item->PLAN_SUM, 0, '.', ' ')." ₸",
                "progress" => $item->PROGRESS
            ]);
        })->first();
    }   
}
