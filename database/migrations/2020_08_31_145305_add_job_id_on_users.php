<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobIdOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table){
    			$table->unsignedBigInteger('job_id')->nullable();
          $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
          $table->dropColumn(['job']);
				});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table){
    			$table->dropColumn(['job_id']);
				});
    }
}
