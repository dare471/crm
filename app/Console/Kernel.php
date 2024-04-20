<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\EmailSenderJob;
use App\Jobs\RetrySmsFailed;
use Illuminate\Support\Facades\Log;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->scheduleEmailSending($schedule);
        $this->scheduleFailedEmailsCheck($schedule);       
    }
    protected function scheduleFailedEmailsCheck(Schedule $schedule)
    {
        $schedule->call(function () {
            try {
                $failedEmails = DB::table('sent_emails')
                    ->where('sent', false)
                    ->get();
                foreach ($failedEmails as $email) {
                   // RetrySmsFailed::dispatch($email);   // Additional actions for handling failed emails
                }
                } catch (\Exception $e) {
                    Log::error("An error occurred while processing failed emails: " . $e->getMessage());
                }
        })->everyFiveMinutes();
    }
    protected function scheduleEmailSending(Schedule $schedule)
    {
        $schedule->call(function () {
            try {
                    $startTime = Carbon::now()->subHours(240);
                    $newDocuments = DB::table('STATUS_PODPISANYA as sp')
                        ->leftJoin('sent_emails as se', 'se.order', '=', 'sp.DOGOVOR_NOMER')
                        ->whereNull('se.order')
                        ->select(
                            'DOGOVOR_GUID as orderGuid',
                            'DOGOVOR_NOMER as order',
                            'KONTRAGENT as clientName',
                            'IIN_BIN as iinBin',
                            'DATA_STATUSA as dateStatus',
                            'NOMER_TELEFONA as tel',
                            'sp.EMAIL as email'
                        )
                        ->where('DATA_STATUSA', '>=', $startTime)
                        ->whereNull('se.order')
                        ->get();
                foreach ($newDocuments as $document) {
                    EmailSenderJob::dispatch($document);
                }
            } catch (\Exception $e) {
                Log::error("An error occurred while comparing tables and sending emails: " . $e->getMessage());
            }
        })->everyMinute();
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
