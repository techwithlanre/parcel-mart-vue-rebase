<?php

namespace App\Console\Commands;

use App\Tasks\TrackingTask;
use Illuminate\Console\Command;

class TrackAramexShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:track-aramex-shipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = new TrackingTask();
        $task->trackAramex();
    }
}
