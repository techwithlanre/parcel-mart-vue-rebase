<?php

namespace App\Console\Commands;

use App\Tasks\TrackingTask;
use Illuminate\Console\Command;

class TrackDhlShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:track-dhl-shipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run DHL Shipment Tracking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = new TrackingTask();
        $track = $task->trackDhl();
        $message = $track ? 'tracking complete w/o errors' : 'tracking complete w/ errors';
        $this->info($message);
    }
}
