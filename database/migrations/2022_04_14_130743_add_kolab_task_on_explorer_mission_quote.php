<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKolabTaskOnExplorerMissionQuote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('explorer_mission_quote', function (Blueprint $table) {
            $table->unsignedBigInteger('kolab_task_id')->nullable();
            $table->foreign('kolab_task_id')->references('id')->on('tasks')->onDelete('set null');
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
        Schema::table('explorer_mission_quote', function (Blueprint $table) {
            $table->dropColumn('kolab_task_id');
        });
    }
}
