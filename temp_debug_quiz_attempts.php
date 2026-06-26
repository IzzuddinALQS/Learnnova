<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$attempts = App\Models\QuizAttempt::where('quiz_id', 7)->get();
echo $attempts->count() . "\n";
foreach ($attempts as $a) {
    echo $a->id . ' ' . $a->quiz_id . ' ' . $a->student_id . ' ' . $a->score . "\n";
}
