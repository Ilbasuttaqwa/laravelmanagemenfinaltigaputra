<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup database for Employee Management System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Employee Management System database...');

        try {
            // Drop existing employees table if exists (with foreign key handling)
            $this->info('Cleaning up existing tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            
            if (Schema::hasTable('employees')) {
                // Drop any foreign key constraints first
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE REFERENCED_TABLE_NAME = 'employees' 
                    AND TABLE_SCHEMA = DATABASE()
                ");
                
                foreach ($foreignKeys as $key) {
                    $tableName = DB::select("
                        SELECT TABLE_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE CONSTRAINT_NAME = '{$key->CONSTRAINT_NAME}' 
                        AND TABLE_SCHEMA = DATABASE()
                        LIMIT 1
                    ");
                    
                    if (!empty($tableName)) {
                        DB::statement("ALTER TABLE {$tableName[0]->TABLE_NAME} DROP FOREIGN KEY {$key->CONSTRAINT_NAME};");
                    }
                }
                
                Schema::dropIfExists('employees');
                $this->info('Dropped existing employees table');
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

            // Run migrations
            $this->info('Running migrations...');
            $this->call('migrate', ['--force' => true]);

            // Run seeders
            $this->info('Seeding database...');
            $this->call('db:seed', ['--force' => true]);

            $this->info('Database setup completed successfully!');
            $this->line('');
            $this->info('Default login credentials:');
            $this->line('Admin: admin@company.com / password');
            $this->line('Manager: manager@company.com / password');
            $this->line('');
            $this->info('You can now run: php artisan serve');

        } catch (\Exception $e) {
            $this->error('Setup failed: ' . $e->getMessage());
            $this->line('');
            $this->info('Please check your database configuration in .env file');
            $this->info('Make sure MySQL is running and database exists');
            return 1;
        }

        return 0;
    }
}
