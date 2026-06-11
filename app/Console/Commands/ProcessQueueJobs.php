<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessQueueJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:auto-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically process queue jobs continuously for email scheduling';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting automatic queue job processing...');
        
        while (true) {
            try {
                // Process all pending jobs
                Artisan::call('queue:work', [
                    '--stop-when-empty' => true,
                    '--max-time' => 60, // Run for 60 seconds max
                    '--memory' => 128,  // Limit memory usage
                ]);
                
                $this->info('Queue jobs processed. Waiting for next cycle...');
                
                // Wait 60 seconds before next cycle
                sleep(60);
                
            } catch (\Exception $e) {
                $this->error('Error processing queue jobs: ' . $e->getMessage());
                sleep(60); // Wait before retrying
            }
        }
        
        return Command::SUCCESS;
    }
}
