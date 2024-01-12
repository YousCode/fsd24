<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTaskTypeTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_type_task', function (Blueprint $table) {
            //
            $table->foreignId("task_id")->references("id")->on("tasks")->onDelete('CASCADE');
            $table->foreignId("task_type_id")->references("id")->on("task_types")->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_task_type_task');
    }
}
