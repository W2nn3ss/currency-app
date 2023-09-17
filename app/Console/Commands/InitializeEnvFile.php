<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeEnvFile extends Command
{
    protected $signature = 'initialize:env';

    protected $description = 'Initialize the .env file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $envExamplePath = base_path('.env.example');
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            copy($envExamplePath, $envPath);
            $this->info('.env file created successfully.');
        } else {
            $this->warn('.env file already exists. No changes were made.');
        }
    }
}
