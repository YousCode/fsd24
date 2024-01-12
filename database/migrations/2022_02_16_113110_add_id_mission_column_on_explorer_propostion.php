<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdMissionColumnOnExplorerPropostion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('explorer_mission_propositions', function (Blueprint $table) {

            $table->unsignedBigInteger('id_mission')->nullable();
            $table->foreign('id_mission')->references('id')->on('explorer_missions')->onDelete('set null');
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
        Schema::table('explorer_mission_propositions', function (Blueprint $table) {
            $table->dropColumn(['id_mission']);
        });
    }
}
