<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Billings\Models\WebhookLog;

foreach (WebhookLog::all() as $log) {
    echo "ID: " . $log->id . " | Topic: " . ($log->payload['topic'] ?? 'no topic') . "\n";
}
unlink(__FILE__);
