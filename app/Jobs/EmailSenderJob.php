<?php

namespace App\Jobs;

use App\Http\Controllers\SMSController;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use App\Models\SentEmails;
use App\Models\Logs;


class EmailSenderJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        // Проверяем, существует ли запись с указанным номером заказа
        $existingRecord = DB::table('sent_emails')->where('order', $this->data->order)->where('sent', false)->first();

        // Если запись существует, обновляем её
        if ($existingRecord) {
            $this->updateRecordAndSendSms($existingRecord);
        } else {
            // Если запись не существует, создаём новую и отправляем SMS
            $this->createRecordAndSendSms();
        }
    }

    // Метод для обновления существующей записи и отправки SMS
    private function updateRecordAndSendSms($record)
    {
        // Отправляем SMS
        $success = $this->sendSms($this->data->tel);
      
        // Обновляем статус 'sent'
        $record->update([
            'sent' => $success,
            'updated_at' => Carbon::now()
        ]);
    }

    // Метод для создания новой записи и отправки SMS
    private function createRecordAndSendSms()
    {
        // Отправляем SMS
        $success = $this->sendSms($this->data->tel);
        Logs::insert([
            'description' => (string)$this->data,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        // Создаём новую запись
        $newRecord = SentEmails::create([
            'orderGuid' => $this->data->orderGuid,
            'order' => $this->data->order,
            'clientName' => $this->data->clientName,
            'iinBin' => $this->data->iinBin,
            'dateStatus' => $this->data->dateStatus,
            'tel' => $this->data->tel,
            'email' => $this->data->email,
            'type' => 'signing the order',
            'sent' => $success,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function sendSms($tel)
    {
    //     $url = 'https://smsc.kz/sys/send.php';
    //     $login = 'AlemAgro';
    //     $password = 'Alemagro2021';
    //     $text = 'Благодарим за подписание заказа! Ваш заказ №' . $this->data->order . '. https://www.survio.com/survey/d/X3J9S5K9V2B9T9N2X';

    //     $data = [
    //         'login' => $login,
    //         'psw' => $password,
    //         'phones' => $tel,
    //         'mes' => urlencode($text),
    //         'cost' => 3,
    //         'fmt' => 3,
    //     ];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url . '?' . http_build_query($data),
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'GET',
    //     ]);

    //     $response = curl_exec($curl);
    //     $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     curl_close($curl);

    //     if ($httpCode === 200) {
    //         $responseData = json_decode($response);
    //         if (!empty($responseData->id)) {
    //             // SMS успешно отправлено
    //             return true;
    //         } else {
    //             // Ошибка при отправке SMS
    //             Logs::insert([
    //                 'description' => (string)$responseData->error,
    //                 'created_at' => Carbon::now(),
    //                 'updated_at' => Carbon::now()
    //             ]);
    //             $errorMessage = 'Неизвестная ошибка';
    //             if (!empty($responseData->error)) {
    //                 $errorMessage = $responseData->error;
    //             }
    //             return false;
    //         }
    //     } else {
    //         Logs::insert([
    //             'description' => (string)$httpCode,
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now()
    //         ]);
    //         return false;
    //     }
    }    
}
