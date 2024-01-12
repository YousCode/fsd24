<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExplorerDriveLinkColOnConversationMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('explorer_mission_conversation_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('id_drive_link')->nullable();
            $table->foreign('id_drive_link')->references('id')->on('explorer_drive_links')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id_drive_link', function (Blueprint $table) {
            $table->dropColumn('id_drive_link');
        });
    }
}
