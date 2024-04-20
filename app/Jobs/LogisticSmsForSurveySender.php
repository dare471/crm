<?php

namespace App\Jobs;

use App\Http\Controllers\SMSController;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SentEmails;

class LogisticSmsForSurveySender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $now = Carbon::now();

        $dateTimeString = $now->format('Y-m-d H:i:s');

        //$controller = SMSController::index($this->data);
        //if(!empty($controller->id)){
            //  SentEmails::query()->insert([
            //     'orderGuid' => $this->data->orderGuid,
            //     'order' => $this->data->order,
            //     'clientName' => $this->data->clientName,
            //     'iinBin' => $this->data->iinBin,
            //     'dateStatus' => $this->data->dateStatus,
            //     'tel' => $this->data->tel,
            //     'email' => $this->data->email,
            //     'type' => 'logistic',
            //     'sent' => true,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ]);
        // }
        //else{
             //  SentEmails::query()->insert([
            //     'orderGuid' => $this->data->orderGuid,
            //     'order' => $this->data->order,
            //     'clientName' => $this->data->clientName,
            //     'iinBin' => $this->data->iinBin,
            //     'dateStatus' => $this->data->dateStatus,
            //     'tel' => $this->data->tel,
            //     'email' => $this->data->email,
            //     'type' => 'logistic',
            //     'sent' => false,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ]);
        //}
        SentEmails::query()->insert([
            'orderGuid' => $this->data->orderGuid,
            'order' => $this->data->order,
            'clientName' => $this->data->clientName,
            'iinBin' => $this->data->iinBin,
            'dateStatus' => $this->data->dateStatus,
            'tel' => $this->data->tel,
            'email' => $this->data->email,
            'type' => 'logistic',
            'sent' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        Log::info("Переданные значение по логистике: " . print_r($this->data, true));
    }
    
}
