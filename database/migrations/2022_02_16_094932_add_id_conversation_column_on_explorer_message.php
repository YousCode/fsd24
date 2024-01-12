<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdConversationColumnOnExplorerMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('explorer_mission_conversation_messages', function (Blueprint $table) {

            $table->unsignedBigInteger('id_conversation')->nullable();
            $table->foreign('id_conversation')->references('id')->on('explorer_mission_conversations')->onDelete('set null');
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
        Schema::table('explorer_mission_conversation_messages', function (Blueprint $table) {
            $table->dropColumn(['id_conversation']);
        });
    }
}
