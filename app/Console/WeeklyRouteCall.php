<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WeeklyRouteCall extends Command
{
    protected $signature = 'route:weekly-call';
    protected $description = 'Call a specific route weekly';

    public function handle()
    {
        // Option 1: Using HTTP client to call your route
        $response = Http::get(url('/nuke-everything'));
        $response2 = Http::get(url('/nuke-everything2'));
        
        // Or if your route requires authentication or other headers
        // $response = Http::withHeaders(['Authorization' => 'Bearer token'])
        //              ->get(url('/your/route'));
        
        $this->info('Route called successfully: ' . $response->status());
        
        $this->info('Route called successfully: ' . $response2->status());
        // Option 2: Alternatively, you can directly call the controller method
        // app()->call('App\Http\Controllers\YourController@yourMethod');
        
        return 0;
    }
}