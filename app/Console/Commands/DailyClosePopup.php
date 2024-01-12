<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\PopupClose;
use App\Http\Helpers\TaskHelper;
use Illuminate\Support\Facades\Auth;

class DailyClosePopup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:closepopup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'at 7PM a popup is displayed to ask if a task should be closed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        event(new PopupClose());

        return Command::SUCCESS;
    }
}
