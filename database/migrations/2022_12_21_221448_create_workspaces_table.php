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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('owner_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        /*Schema::table('users', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->references('id')->on('workspaces');
        });*/

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('workspace');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->references('id')->on('workspaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace');

        /*Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['workspace']);
            $table->dropColumn('workspace');
        });*/

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('workspace');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['workspace']);
            $table->dropColumn('workspace');
        });
    }
};
