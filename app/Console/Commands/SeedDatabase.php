<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Console\Command;

class SeedDatabase extends Command
{
    protected $signature = 'app:seed-database
                            {--fresh : Drop all tables and re-run all migrations before seeding}
                            {--force : Force the operation to run when in production}';

    protected $description = 'Seed the database with default data (categories and exercises)';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->info('Dropping all tables and re-running migrations...');
            $this->call('migrate:fresh', ['--force' => $this->option('force')]);
        }

        $this->info('Seeding database...');
        $this->call('db:seed', ['--force' => $this->option('force')]);

        $exercises = Exercise::count();
        $categories = Category::count();

        $this->info('Database seeded successfully!');
        $this->line("  Categories: {$categories}");
        $this->line("  Exercises: {$exercises}");

        return Command::SUCCESS;
    }
}
