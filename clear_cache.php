<?php
try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    echo "<br>Starting cache clearing process...<br>";
    $commands = [
        'route:clear' => 'Route cache cleared!', // Use the default route:clear command
        'config:clear' => 'Configuration cache cleared!',
        'view:clear' => 'View cache cleared!',
        'cache:clear' => 'Application cache cleared!',
        'event:clear' => 'Event cache cleared!',
    ];

    foreach ($commands as $command => $message) {
        $kernel->call($command);
        echo "$message<br>";
    }

    echo "<br>All cache clearing operations completed successfully!<br>";
} catch (Exception $e) {
    echo "Error occurred: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    exit(1);
}

echo "<br>Environment Information:<br>";
echo "Laravel Version: " . $app->version() . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Environment: " . $app->environment() . "<br>";
echo "Timestamp: " . date('Y-m-d H:i:s') . "<br>";