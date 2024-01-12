<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKolabProjectOnExplorerMissionProposition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('explorer_mission_propositions', function (Blueprint $table) {
            $table->unsignedBigInteger('kolab_project_id')->nullable();
            $table->foreign('kolab_project_id')->references('id')->on('projects')->onDelete('set null');
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
            $table->dropColumn('kolab_project_id');
        });
    }
}
