<?php

namespace App\Jobs;

use App\Models\SentEmails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Logs;
use Carbon\Carbon;

class RetrySmsFailed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
            SentEmails::where('order', $this->data->order)
            ->update([
                'sent' => $this->sendSms( $this->data->tel),
            ]);
            Logs::insert([
                'id' => 2,
                'description' => "succes". $this->data->order."",
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ]);
        return true;
        //
    }

    public function sendSms($tel)
    {
        // $url = 'https://smsc.kz/sys/send.php';
        // $login = 'AlemAgro';
        // $password = 'Alemagro2021';
        // $text = 'Благодарим за подписание заказа! Ваш заказ №' . $this->data->order . '. https://www.survio.com/survey/d/X3J9S5K9V2B9T9N2X';

        // $data = [
        //     'login' => $login,
        //     'psw' => $password,
        //     'phones' => $tel,
        //     'mes' => urlencode($text),
        //     'cost' => 3,
        //     'fmt' => 3,
        // ];

        // $curl = curl_init();

        // curl_setopt_array($curl, [
        //     CURLOPT_URL => $url . '?' . http_build_query($data),
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        // ]);

        // $response = curl_exec($curl);
        // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // curl_close($curl);

        // if ($httpCode === 200) {
        //     $responseData = json_decode($response);
        //     if (!empty($responseData->id)) {
        //         // SMS успешно отправлено
        //         return true;
        //     } else {
        //         // Ошибка при отправке SMS
        //         $errorMessage = 'Неизвестная ошибка';
        //         if (!empty($responseData->error)) {
        //             $errorMessage = $responseData->error;
        //         }
        //         return false;
        //     }
        // } else {
        //     return false;
        // }
    } 
}
