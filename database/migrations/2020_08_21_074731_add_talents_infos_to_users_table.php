<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTalentsInfosToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('lastname');
            $table->string("firstname");
            $table->string("phone");
            $table->string("job");
            $table->string("price");
            $table->string("city");
            $table->string("country");
            $table->string("profile_type");
            $table->string("profile_url");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname', 'phone', 'job', 'price', 'city', 'country', 'profile_type', 'profile_url']);
        });
    }
}
