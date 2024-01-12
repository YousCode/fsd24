<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Helpers\ExplorerHelper;

class ExplorerDailyMissionCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'every day at 18am, check if a mission paid can be closed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $explorerHelper = new ExplorerHelper();
        $explorerHelper->checkPaidMission();

        return Command::SUCCESS;
    }
}
