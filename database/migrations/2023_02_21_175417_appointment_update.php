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
        if(Schema::hasTable('appointments'))
        {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropForeign('appointments_room_id_foreign');
                $table->dropColumn(['room_id']);
            });

            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn(['lastname', 'firstname', 'job', 'phone', 'email', 'status']);
                $table->string('title');
            });
        }
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
};
