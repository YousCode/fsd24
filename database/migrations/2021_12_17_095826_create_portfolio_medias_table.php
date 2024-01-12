<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_medias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('uniqid')->nullable();
            $table->string('url_view')->nullable();
            $table->string('url_download')->nullable();

            $table->string('media_type')->nullable();
            $table->string('external_url')->nullable();

            $table->unsignedBigInteger('portfolio_id')->nullable();
            $table->foreign('portfolio_id')->references('id')->on('portfolios')->onDelete('CASCADE');

            $table->timestamps();
            $table->integer("created_by");
            $table->integer("updated_by");
            $table->integer("deleted_by");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolios_medias');
    }
}
