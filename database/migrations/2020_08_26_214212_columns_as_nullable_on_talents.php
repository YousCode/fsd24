<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ColumnsAsNullableOnTalents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table){
        	$table->string('phone')->nullable()->change();
    			$table->string('job')->nullable()->change();
    			$table->string('price')->nullable()->change();
    			$table->string('city')->nullable()->change();
    			$table->string('country')->nullable()->change();
    			$table->string('profile_type')->nullable()->change();
    			$table->string('profile_url')->nullable()->change();
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
    }
}
