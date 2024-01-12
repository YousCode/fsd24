<?php

namespace App\Console;

use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Events\UpdateMissionStatusExplorer;
use App\Http\Services\Explorer\ExplorerMessengerConversationService;
use App\Http\Services\Explorer\ExplorerMissionService;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DailyClose;
use App\Console\Commands\DailyClosePopup;
use App\Console\Commands\ExplorerDailyMissionCheck;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DailyClose::class,
        DailyClosePopup::class,
        ExplorerDailyMissionCheck::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ?? changing dans l'ideal ici Tony je pense que je passerais par une constraign en utilisant pusher pour get le status des propositions en temps réel
        // dans la doc j'ai vu le ->when($callback) qui pourrait être utile mais pas capté le fonctionnement du callback dans mon cas


        //schedule function callback
        /*$schedule->call(function () {
            //not sure but will use it to get the conversation where i want to send the message
            $explorerMessengerConversationService = new ExplorerMessengerConversationService();
            $explorerMissionService= new ExplorerMissionService($explorerMessengerConversationService);
            //date of the day
            $now = Carbon::now()->format('Y-m-d 00:00:00');
            $quote = ExplorerMissionQuote::where('deadline','=',$now)->get();
            //getting all quotes where deadline is over
            foreach ($quote as $quotes)
            {
                $proposition = ExplorerMissionProposition::where('id',$quotes->id_proposition)->first();
                if($quotes->deadline === $now)
                {
                    //sending the system message if the deadline is over
                    $explorerMissionService->workFinishedMissionProposition($proposition);
                }
            }
        })->everyMinute();*/
        $schedule->command('daily:closepopup')->dailyAt('18:00');
        $schedule->command('daily:close')->dailyAt('23:59');
        $schedule->command('explorer:check')->dailyAt('18:00');
        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
