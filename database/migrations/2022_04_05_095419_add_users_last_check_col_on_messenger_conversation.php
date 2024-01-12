<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersLastCheckColOnMessengerConversation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('explorer_mission_conversations', function (Blueprint $table) {
            $table->dateTime('last_freelancer_check')->nullable();
            $table->dateTime('last_client_check')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('explorer_mission_conversations', function (Blueprint $table) {
            $table->dropColumn(['last_freelancer_check', 'last_client_check']);
        });
    }
}
