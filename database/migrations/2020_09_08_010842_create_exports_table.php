<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('duration');

            $table->unsignedBigInteger('resolution_id')->nullable();
            $table->foreign('resolution_id')->references('id')->on('resolutions')->onDelete('SET NULL');

            $table->unsignedBigInteger('format_id')->nullable();
            $table->foreign('format_id')->references('id')->on('export_formats')->onDelete('SET NULL');

            $table->unsignedBigInteger('language_id')->nullable();
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('SET NULL');

            $table->timestamps();
            $table->integer("created_by");
            $table->integer("updated_by");
            $table->integer("deleted_by");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exports');
    }
}
