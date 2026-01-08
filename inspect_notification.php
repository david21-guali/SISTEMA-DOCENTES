<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$comments = \App\Models\Comment::all();
$orphanedCount = 0;
$validCount = 0;

echo "Total Comments in DB: " . $comments->count() . "\n";

foreach ($comments as $comment) {
    if (!$comment->commentable) {
        echo "Deleting Orphaned Comment ID: {$comment->id} (Target NOT FOUND)...\n";
        $comment->delete();
        $orphanedCount++;
    } else {
        $validCount++;
    }
}

echo "Cleanup Complete. Deleted: $orphanedCount, Remaining Valid: $validCount\n";
