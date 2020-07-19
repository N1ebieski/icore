<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInBansModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bans_models', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'bans_models_model_id_model_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bans_models', function (Blueprint $table) {
            $table->dropIndex('bans_models_model_id_model_type_index');
        });
    }
}
