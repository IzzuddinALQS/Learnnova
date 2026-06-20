<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$router = $app->make('router');

try {
    echo route('quizzes.attempts.take', 7) . "\n";
    echo route('quizzes.attempts.store', 7) . "\n";
    echo route('quizzes.attempts.show', [7, 1]) . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
