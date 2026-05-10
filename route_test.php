<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
try {
    echo route('admin.exams.save-marks', ['exam' => 6, 'subject' => 21]) . PHP_EOL;
} catch (Exception $e) {
    echo 'EX: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}
try {
    echo route('core.exams.marks.save', ['exam' => 6, 'subject' => 21]) . PHP_EOL;
} catch (Exception $e) {
    echo 'EX2: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}
