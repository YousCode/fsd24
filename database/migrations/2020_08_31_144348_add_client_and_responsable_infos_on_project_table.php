<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientAndResponsableInfosOnProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            //Clients
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();

            //Responsables
            $table->string('responsable_name')->nullable();
            $table->string('responsable_phone')->nullable();
            $table->string('responsable_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            //Clients
            $table->dropColumn('client_phone');
            $table->dropColumn('client_email');

            //Responsables
            $table->dropColumn('responsable_name');
            $table->dropColumn('responsable_phone');
            $table->dropColumn('responsable_email');
        });
    }
}
