<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyUsersOnStockSummary;
use Illuminate\Database\ConnectionInterface as DB;

class SendMailsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily summary emails to users';

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
        $users = User::all();

        $notification = new NotifyUsersOnStockSummary($this->generateSummary());

        Notification::send($users, $notification);

        $this->info('Summary emails sent successfully.');
    }

    private function generateSummary()
    {
        //demonstrating the use of queries without the ORM
        $summary = DB::select("SELECT
                                            name,
                                            new_price AS current_price,
                                            ( (new_price - old_price) / old_price ) * 100 AS percentage_increase
                                        FROM (
                                            SELECT
                                                name,
                                                MAX(price) AS new_price,
                                                (
                                                    SELECT price
                                                    FROM stocks AS s2
                                                    WHERE s2.name = s1.name
                                                    ORDER BY s2.id DESC
                                                    LIMIT 1 OFFSET 1
                                                ) AS old_price
                                            FROM stocks AS s1
                                            GROUP BY name
                                            HAVING COUNT(*) >= 2
                                        ) AS subquery;
                                        ");

    return json_encode($summary);
    }
}
