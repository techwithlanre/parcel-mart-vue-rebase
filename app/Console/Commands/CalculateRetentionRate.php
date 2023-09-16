<?php

namespace App\Console\Commands;

use App\Models\UserActivity;
use Illuminate\Console\Command;

class CalculateRetentionRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:retention-rate {days=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate user retention rate over a specified number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $startDate = now()->subDays($days);
        $newUsers = UserActivity::where('login_at', '>=', $startDate)->distinct('user_id')->count();
        $returningUsers = UserActivity::where('login_at', '>=', $startDate)->count();
        if ($returningUsers > 0 && $newUsers > 0) {
            $retentionRate = ($returningUsers / $newUsers) * 100;
            $this->info($retentionRate);
        } else {
            $this->info("This app is still new");
        }
    }
}
