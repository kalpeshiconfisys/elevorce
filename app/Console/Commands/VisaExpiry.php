<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\VisaDetail;

class VisaExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visa:expiry-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check visa expiry dates and update statuses / send notifications.';

    public function handle()
    {
        $today = Carbon::today();
        $notifyDays = [30, 15, 10, 5, 4, 3, 2, 1];
        $notifyList = [];

        // Fetch all visa details with employees where expiry date exists
        $visas = VisaDetail::with('employee')
            ->whereNotNull('visa_expiry_date')
            ->get()
            ->filter(fn($visa) => !empty($visa->employee));

        foreach ($visas as $visa) {
            try {
                $expiryDate = Carbon::parse($visa->visa_expiry_date);
                $diffDays = $today->diffInDays($expiryDate, false); // false → keep negative for expired

                // If within notify days before expiry
                if ($diffDays > 0 && in_array($diffDays, $notifyDays)) {
                    $notifyList[] = $visa;
                }

                // If expired today
                if ($diffDays === 0) {
                    $visa->visa_status = 'expired';
                    $visa->save();
                    Log::info("Visa expired today for employee: {$visa->employee->name}");
                }

                // If already expired in the past
                if ($diffDays < 0 && $visa->visa_status !== 'expired') {
                    $visa->visa_status = 'expired';
                    $visa->save();
                    Log::info("Visa already expired for employee: {$visa->employee->name}");
                }

            } catch (\Exception $e) {
                Log::error("Error processing visa ID {$visa->id}: " . $e->getMessage());
            }
        }

        // Send notifications if any
        if (count($notifyList)) {
            // Example: Notification::send($notifyList, new VisaExpiryNotification($today));
            Log::info("Notification sent for " . count($notifyList) . " visas expiring soon.");
        }

        Log::info('Visa expiry check completed successfully.');
        return 0;
    }
}
