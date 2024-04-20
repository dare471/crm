<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\EmailSenderJob;
use App\Models\Logs;
use App\Models\SentEmails;
use Carbon\Carbon;

class EmailSendController extends Controller
{
    public function EmailSender(Request $request){

        Logs::insert([
            'description' => '222',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
            
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://smsc.kz/sys/send.php?login=AlemAgro&psw=Alemagro2021&phones=87474586397&mes=%D0%91%D0%BB%D0%B0%D0%B3%D0%BE%D0%B4%D0%B0%D1%80%D0%B8%D0%BC%20%D0%92%D0%B0%D1%81%20%D0%B7%D0%B0%20%D0%B2%D1%8B%D0%B1%D0%BE%D1%80%20%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8%20%D0%90%D0%BB%D0%B5%D0%BC%D0%90%D0%B3%D1%80%D0%BE.%20%D0%9E%D1%86%D0%B5%D0%BD%D0%B8%D1%82%D0%B5%2C%20%D0%BD%D0%B0%D1%81%D0%BA%D0%BE%D0%BB%D1%8C%D0%BA%D0%BE%20%D0%92%D1%8B%20%D1%83%D0%B4%D0%BE%D0%B2%D0%BB%D0%B5%D1%82%D0%B2%D0%BE%D1%80%D0%B5%D0%BD%D1%8B%20%D1%8D%D0%BA%D1%81%D0%BF%D0%B5%D1%80%D1%82%D0%BD%D0%BE%D1%81%D1%82%D1%8C%D1%8E%20%D0%BC%D0%B5%D0%BD%D0%B5%D0%B4%D0%B6%D0%B5%D1%80%D0%B0%20%D0%BF%D0%BE%20%D0%BF%D1%80%D0%BE%D0%B4%D0%B0%D0%B6%D0%B0%D0%BC%20%D0%BF%D0%BE%20%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B5%20https%3A%2F%2Fwww.survio.com%2Fsurvey%2Fd%2FX3J9S5K9V2B9T9N2X&cost=3&fmt=3',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);

        // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // curl_close($curl);

        // if ($httpCode === 200) {
        //     $responseData = json_decode($response, true);
        //     LogsModel::insert([
        //         'description' => (string)$responseData,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ]);
        //     return $responseData;
        //     // Check for specific property containing the message
        //     if (isset($responseData->error)) {
        //         $message = $responseData->error;
        //     } else {
        //         // If no message property, use the entire response (proceed with caution)
        //         $message = $responseData;
        //     }
        //     // Insert log with extracted message
        // } else {
        //     LogsModel::insert([
        //         'description' => (string)$httpCode,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now()
        //     ]);
        //     return response()->json([false]);
        // }
    }
}
