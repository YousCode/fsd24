<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPropositionColumnOnExplorerQuote extends Migration
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
            $table->unsignedBigInteger('id_proposition')->nullable();
            $table->foreign('id_proposition')->references('id')->on('explorer_mission_propositions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('explorer_mission_quote', function (Blueprint $table) {
            $table->dropColumn(['id_proposition']);
        });
    }
}
