<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExplorerDriveLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explorer_drive_links', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_drive')->nullable();
            $table->foreign('id_drive')->references('id')->on('explorer_drives')->onDelete('set null');

            $table->string('name');
            $table->string('link');
            $table->string('miniature')->nullable();
            $table->integer("created_by");
            $table->integer("updated_by");
            $table->integer("deleted_by");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('explorer_drive_links');
    }
}
