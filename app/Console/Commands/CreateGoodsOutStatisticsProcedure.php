<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateGoodsOutStatisticsProcedure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goods-out:create-procedure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the Goods Out Statistics stored procedure';

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
        $this->info('Creating Goods Out Statistics stored procedure...');
        
        try {
            $sqlFile = database_path('migrations/create_goods_out_statistics_procedure.sql');
            
            if (!File::exists($sqlFile)) {
                $this->error('SQL file not found: ' . $sqlFile);
                return 1;
            }
            
            $sql = File::get($sqlFile);
            
            // Execute the SQL
            DB::unprepared($sql);
            
            $this->info('✅ Goods Out Statistics stored procedure created successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error creating stored procedure: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
