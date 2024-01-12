<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeInfosOnExplorerQuote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('explorer_mission_quote', function (Blueprint $table) {
            $table->longText('stripe_cs_id')->nullable();
            $table->dateTime('init_payment_date')->nullable();
            $table->dateTime('quote_paid_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('explorer_mission_quote', function (Blueprint $table) {
            $table->dropColumn(['stripe_cs_id','init_payment_date','quote_paid_date']);
        });
    }
}
