<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateEmbeddingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $python = "C:\Users\iconfisys\AppData\Local\Microsoft\WindowsApps\python.exe";
        $script = "D:\\xampp\\htdocs\\elevorce\\public\\script\\generate_and_store_embeddings.py";

        $process = new \Symfony\Component\Process\Process([$python, $script]);
        $process->setTimeout(120);

        try {
            $process->mustRun();
            Log::info("✅ Python job success", [
                'stdout' => $process->getOutput(),
            ]);
        } catch (\Throwable $e) {
            Log::error("❌ Python job failed", [
                'exception' => $e->getMessage(),
                'stderr'    => $process->getErrorOutput(),
                'stdout'    => $process->getOutput(),
                'command'   => $process->getCommandLine(),
            ]);
        }
    }
}
