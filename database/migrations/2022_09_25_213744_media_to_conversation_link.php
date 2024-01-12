<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MediaToConversationLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->unsignedBigInteger('explorer_mission_conversation_message_id')->nullable();
            $table->foreign('explorer_mission_conversation_message_id')->references('id')->on('explorer_mission_conversation_messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->dropColumn('explorer_mission_conversation_message_id');
        });
    }
}
