<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PushFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload format excel to storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = __DIR__ . "/../../../format/";
        $files = scandir($path);
        Artisan::call('storage:link');
        $uploadDir = __DIR__ . "/../../../storage/app/test/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir);
        }
        $this->info('loading...');
        foreach (array_slice($files, 2) as $file) {
            copy($path . $file, $uploadDir . $file);
        }
        sleep(1);
        $this->info('success upload format to storage, full senyum :)');
    }
}
