<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTagsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('taggable.connection');

        if (!Schema::connection($connection)->hasTable('tags')) {
            Schema::connection($connection)->create('tags', function (Blueprint $table) {
                $table->bigIncrements('tag_id');
                $table->string('name');
                $table->string('normalized');
                $table->timestamps();

                $table->index('normalized');
            });
        }

        // Full Text Index
        DB::statement('ALTER TABLE tags ADD FULLTEXT fulltext_index (name)');

        Schema::connection($connection)->create('tags_models', function (Blueprint $table) {
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->timestamps();

            $table->index(['tag_id', 'model_id'], 'i_taggable_fwd');
            $table->index(['model_id', 'tag_id'], 'i_taggable_rev');
            $table->index('model_type', 'i_taggable_type');
        });

        // Dropujemy niepotrzebną tabelę, bo Taggable ma hardcodowane migracje (nie wiem czemu?)
        if (Schema::connection($connection)->hasTable('taggable_taggables')) {
            Schema::connection($connection)->drop('taggable_taggables');
        }

        // Dropujemy niepotrzebną tabelę, bo Taggable ma hardcodowane migracje (nie wiem czemu?)
        if (Schema::connection($connection)->hasTable('taggable_tags')) {
            Schema::connection($connection)->drop('taggable_tags');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('taggable.connection');

        Schema::connection($connection)->drop('tags_models');

        Schema::connection($connection)->drop('tags');
    }
}
