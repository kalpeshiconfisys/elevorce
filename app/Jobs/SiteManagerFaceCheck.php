<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SiteManagerFaceCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle($parameter)
    {
        $output = shell_exec("python3" . escapeshellarg(base_path('public/scripts/face_identification_get_new_user_for_photographer_images.py')) . " " . escapeshellarg($parameter));

        return $output ? json_decode($output, true) : ['status' => 'error', 'message' => 'No output'];
    }
}
