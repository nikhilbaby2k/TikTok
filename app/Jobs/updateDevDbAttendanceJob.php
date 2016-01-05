<?php

namespace App\Jobs;

use App\Http\Controllers\TikTokController;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class updateDevDbAttendanceJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            $this->release(30);
        }
        print_r("\nAttempts: ". $this->attempts(). "\n");
        $tik_tok_controller = \App::make(TikTokController::class);
        $tik_tok_controller->manage();
    }
}
