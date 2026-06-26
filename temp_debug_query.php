<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$attempt = App\Models\QuizAttempt::with(['student','answers.question.options','answers.option'])->where('quiz_id',7)->find(1);
var_dump($attempt ? $attempt->id : null);
