<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToTagsModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags_models', function (Blueprint $table) {
            $table->foreign('tag_id', 'tags_models_tag_id_foreign')
                ->references('tag_id')
                ->on('tags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags_models', function (Blueprint $table) {
            $table->dropForeign('tags_models_tag_id_foreign');
        });
    }
}
