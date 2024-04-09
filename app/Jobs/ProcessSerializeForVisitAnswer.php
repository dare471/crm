<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\PlannedMeetingForMobileController;
class ProcessSerializeForVisitAnswer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $controller;
    public function __construct(PlannedMeetingForMobileController $controller, $data)
    {
        $this->controller = $controller;
        $this->$data = $data;
    }

    public function handle()
    {
        $this->controller->serializeAnswerVisit($this->data);
    }
}
