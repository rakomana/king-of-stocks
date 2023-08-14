<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stock;

class StockControllerTest extends TestCase
{
    use RefreshDatabase; // Reset the database before each test

    public function testStoreMethod()
    {
        // Simulate a successful HTTP response from the external API
        $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withoutVerifying')
                 ->once()
                 ->andReturnSelf();
            $mock->shouldReceive('get')
                 ->once()
                 ->with('https://financialmodelingprep.com/api/v3/quote-short/AAPL', [
                     'apikey' => '3607f50af9251f1c1ebaab9b71fdfb74',
                 ])
                 ->andReturn($this->successfulResponse());
        });

        // Simulate the request
        $response = $this->postJson(route('stock.store'));

        // Assert that the response is successful
        $response->assertSuccessful();
    }

    public function testShowMethod()
    {
        // Create a test stock
        $stock = Stock::factory()->create(['name' => 'AAPL']);

        // Simulate the request with query parameters
        $response = $this->getJson(route('stock.show', ['name' => 'AAPL']));

        // Assert that the response is successful
        $response->assertSuccessful();

        // Add more assertions based on your implementation
    }

    public function testSendEmailMethod()
    {
        // Create a test user
        $user = User::factory()->create();

        // Mock the generateSummary method to return sample summary data
        $this->mock(StockController::class, function ($mock) {
            $mock->shouldReceive('generateSummary')
                ->once()
                ->andReturn(json_encode([
                    [
                        'name' => 'AAPL',
                        'current_price' => 177.79,
                        'percentage_increase' => 10,
                    ],
                ]));
        });

        // Mock the Notification facade to prevent actual notifications
        Notification::fake();

        // Call the sendEmail method
        $response = $this->getJson(route('stock.sendEmail'));

        // Assert that the response is successful
        $response->assertSuccessful();

        // Assert that the notification was sent to the user
        Notification::assertSentTo($user, NotifyUsersOnStockSummary::class);

        // Add more assertions based on your implementation
    }

    public function testCleanUpMethod()
    {
        // Create a test stock with an old date
        $oldStock = Stock::factory()->create(['created_at' => Carbon::now()->subDays(61)]);

        // Create a test stock within the cutoff date
        $recentStock = Stock::factory()->create();

        // Call the cleanUp method
        $response = $this->getJson(route('stock.cleanUp'));

        // Assert that the response is successful
        $response->assertSuccessful();

        // Assert that the old stock record has been deleted
        $this->assertDatabaseMissing('stocks', ['id' => $oldStock->id]);

        // Assert that the recent stock record still exists
        $this->assertDatabaseHas('stocks', ['id' => $recentStock->id]);
    }

    public function testGenerateSummaryMethod()
    {
        // Your test code for the generateSummary method
        // You can call the method and make assertions on the result
    }

      // Helper method to generate a mock successful response
      private function successfulResponse()
      {
          return new \Illuminate\Http\Client\Response(json_encode([
              [
                  'symbol' => 'AAPL',
                  'price' => 177.79,
              ],
          ]), 200);
      }
}