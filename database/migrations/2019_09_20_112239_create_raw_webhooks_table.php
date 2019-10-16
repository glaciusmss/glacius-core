<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_webhooks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('raw_data');
            $table->unsignedBigInteger('marketplace_id');
            $table->timestamps();
            $table->foreign('marketplace_id')->references('id')->on('marketplaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_webhooks');
    }
}
