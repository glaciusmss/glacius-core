<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('event');
            $table->integer('direction');
            $table->integer('state');
            $table->text('error_msg')->nullable();
            $table->morphs('sync_transactional', 'sync_transactions_transactional_index');
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
        Schema::dropIfExists('sync_transactions');
    }
}
