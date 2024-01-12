<?php

namespace App\Providers;

use App\Events\MessageAdded;
use App\Models\ExplorerMissionConversationMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*View::composer('*', function($view) {
            $lastConversation = DB::table('explorer_mission_conversation_messages')->select('id_conversation')->where('created_by', '=', auth()->user()?->id)->orderBy('created_at', 'DESC')->first();
            if (auth()->user()){
                $lastMessage = DB::table('explorer_mission_conversation_messages')->select('*')->whereNotNull('id_conversation')->where('mark_as_read','=',0)->orderBy('created_at', 'DESC')->first();
                $view->with('lastMessage',$lastMessage);
                if($lastConversation !== null)
                {
                    $lastMessage = DB::table('explorer_mission_conversation_messages')->select('*')->where('id_conversation', '=', $lastConversation->id_conversation)->where('mark_as_read','=',0)->orderBy('created_at', 'DESC')->first();
                    event(new MessageAdded());
                    $view->with('lastMessage',$lastMessage);
                }
            }
        });*/

        Schema::defaultStringLength(191);
        Carbon::setLocale(config('app.locale'));
    }
}
