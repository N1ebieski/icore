<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bans_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type')->index();
            $table->bigInteger('model_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('bans_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->index();
            $table->string('value');
            $table->timestamps();
        });

        // Full Text Index
        DB::statement('ALTER TABLE bans_values ADD FULLTEXT fulltext_index (value)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bans_models');
        Schema::dropIfExists('bans_values');
    }
}
