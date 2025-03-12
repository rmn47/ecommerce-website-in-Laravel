<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ForceClearRouteCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:force-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forcefully clear the route cache, ignoring duplicate route names';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function handle(Filesystem $files)
    {
        $path = $this->laravel->getCachedRoutesPath();

        if ($files->exists($path)) {
            $files->delete($path);
        }

        $this->info('Route cache cleared forcefully!');
    }
}