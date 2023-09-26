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
    protected $description = 'Run DHL shipment tracking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = new TrackingTask();
        $task->trackDhl();
    }
}
