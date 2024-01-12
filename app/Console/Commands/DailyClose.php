<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Helpers\TaskHelper;

class DailyClose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the task every day at 19:00';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $taskHelper = new TaskHelper();
        $taskHelper->closeTask();

        return Command::SUCCESS;
    }
}
