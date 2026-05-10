<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exam = App\Models\CoreExam::find(6);
$subject = App\Models\CoreExamSubject::find(21);

echo "exam=\n";
var_dump($exam ? $exam->toArray() : null);
echo "subject=\n";
var_dump($subject ? $subject->toArray() : null);
