<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupCityImages extends Command
{
    protected $signature = 'images:cleanup';
    protected $description = 'Clean up city images older than 24 hours';

    public function handle()
    {
        $files = Storage::disk('public')->files('cities');
        $count = 0;

        foreach ($files as $file) {
            $timestamp = (int) str_replace(['.jpg'], '', substr($file, strrpos($file, '_') + 1));
            if (Carbon::createFromTimestamp($timestamp)->addHours(24)->isPast()) {
                Storage::disk('public')->delete($file);
                $count++;
            }
        }

        $this->info("Cleaned up {$count} old city images");
    }
}
