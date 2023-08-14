<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stock;
use App\Enum\ResponseCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyUsersOnStockSummary;
use Illuminate\Database\ConnectionInterface as DB;

class StockController extends Controller
{
    public $db;
    public $user;
    public $stock;

    /**
     * Inject Model(s) into the constructor
    */
    public function __construct(DB $db, Stock $stock, User $user)
    {
        $this->db = $db;
        $this->user = $user;
        $this->stock = $stock;
    }

    /**
     * At 5 minutes before the exchanges close each day, retrieve the latest stock(AAPL,GOOG,SPY,CRM,TSLA)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $symbols = ['AAPL', 'GOOG', 'SPY', 'CRM', 'TSLA'];

        //loop through the symbols
        foreach($symbols as $symbol) {
            $response = Http::withoutVerifying()->get('https://financialmodelingprep.com/api/v3/quote-short/'.$symbol, [
                'apikey' => '3607f50af9251f1c1ebaab9b71fdfb74',
            ]);
    
            if ($response->successful()) {
                
                foreach(json_decode($response) as $res) {
                    $this->db->beginTransaction();
    
                    $stock = new $this->stock();
                    $stock->name = $res->symbol;
                    $stock->price = $res->price;
                    $stock->save();
    
                    $this->db->commit();
    
                    //log the execution to Log for debugging purpose
                    Log::info($symbol.' ran successfully');
                }
            } else {
                Log::info($symbol.' did not run successfully');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $stock = $this->stock->whereName($request->name)->get();

        if ($request->has('fromDate') && $request->has('toDate')) {
            $fromDate = Carbon::parse($request->fromDate);
            $toDate = Carbon::parse($request->toDate);

            if($toDate < $fromDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range',
                    'status' => ResponseCodes::UNAUTHORIZED
                ]);
            }

            $stock->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if($request->has('order_by')) {
            $stock->orderBy('created_at', $request->order_by);
        } else {
            $stock->orderBy('created_at', 'desc');
        }

        $filteredProducts = $stock;

        if($filteredProducts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Oops!, ticker does not exists',
                'status' => ResponseCodes::UNAUTHORIZED
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $filteredProducts,
            'status' => ResponseCodes::HTTP_OK
        ]);
    }

    /**
     * At exactly 22:00 UTC each evening send email with a summary of the latest stock price. price of each ticker and increase
     */
    public function sendEmail()
    {
        $users = $this->user->all();

        $notification = new NotifyUsersOnStockSummary();
    
        Notification::send($users, $notification);
    
        Log::info('Emails sent');
    }

    //cleanup of old prices older than 60 days
    public function cleanUp()
    {
        $cutoffDate = Carbon::now()->subDays(60);

        $this->stock->where('created_at', '<', $cutoffDate)->delete();

        Log::info('Clean Up Done');
    }

    //run unit test
    //readme
}
