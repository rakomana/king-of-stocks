<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanUpCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old stock records older than 60 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(60);

        Stock::where('created_at', '<', $cutoffDate)->delete();

        Log::info('Clean Up Done');
    }
}
