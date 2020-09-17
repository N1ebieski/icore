<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('stats_values', function (Blueprint $table) {
            $table->bigInteger('stat_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->bigInteger('value')->index();

            $table->index(['model_type', 'model_id']);

            $table->foreign('stat_id')
                ->references('id')
                ->on('stats')
                ->onDelete('cascade');

            $table->primary(['stat_id', 'model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stats');
        Schema::dropIfExists('stats_values');
    }
}
