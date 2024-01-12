<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_historic', function (Blueprint $table) {
            $table->id();
            $table->foreignId("from_user_id")->references("id")->on("users");
            $table->foreignId("to_user_id")->references("id")->on("users");
            $table->foreignId("task_id")->references("id")->on("tasks");
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
        Schema::dropIfExists('tasks_historic');
    }
};
