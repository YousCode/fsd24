<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('project_files', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('path');
        $table->string('extension');
        $table->string('uniqid');
        $table->string('url_view');
        $table->string('url_download');

        $table->unsignedBigInteger('project_id')->nullable();
        $table->foreign('project_id')->references('id')->on('projects')->onDelete('CASCADE');

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
      Schema::dropIfExists('project_files');
    }
}
