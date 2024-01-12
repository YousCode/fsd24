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
        Schema::dropIfExists('appointments_talent');
        // A know user
        Schema::create('appointment_talent', function (Blueprint $table) {
            $table->foreignId("appointment_id")->references("id")->on("appointments")->onDelete('cascade');
            $table->foreignId("user_id")->references("id")->on("users")->onDelete('cascade');
            $table->unique(['appointment_id', 'user_id']);
        });

        // Just an email
        Schema::create('appointment_contact', function (Blueprint $table) {
            $table->foreignId("appointment_id")->references("id")->on("appointments")->onDelete('cascade');
            $table->foreignId("contact_id")->references("id")->on("contact")->onDelete('cascade');
            $table->unique(['appointment_id', 'contact_id']);
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
};
