<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeApiSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat api secret untuk aplikasi sip point';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $key = Str::random(64);
        if (file_exists($path = $this->envPath()) === false) {
            $this->info('.env belum di buat');
        } else {

            if (Str::contains(file_get_contents($path), 'API_SECRET') === false && Str::contains(file_get_contents($path), 'VITE_API_SECRET') === false) {
                file_put_contents($path, PHP_EOL . "API_SECRET=$key" . PHP_EOL, FILE_APPEND);
                file_put_contents($path, PHP_EOL . "VITE_API_SECRET=$key" . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents($path, preg_replace("/API_SECRET=.+/i", 'API_SECRET=' . $key, file_get_contents($path)));
                file_put_contents($path, preg_replace("/VITE_API_SECRET=.+/i", 'VITE_API_SECRET=' . $key, file_get_contents($path)));
            }
        }

        $this->info('berhasil membuat api secret');
    }

    protected function envPath()
    {
        if (method_exists($this->laravel, 'environmentFilePath')) {
            return $this->laravel->environmentFilePath();
        }

        // check if laravel version Less than 5.4.17
        if (version_compare($this->laravel->version(), '5.4.17', '<')) {
            return $this->laravel->basePath() . DIRECTORY_SEPARATOR . '.env';
        }

        return $this->laravel->basePath('.env');
    }
}
