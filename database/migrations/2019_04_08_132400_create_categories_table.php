<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('name');
            $table->tinyInteger('status')->unsigned();
            $table->bigInteger('parent_id')->unsigned()->index()->nullable();
            $table->integer('position', false, true);
            $table->integer('real_depth', false, true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->index(['id', 'model_type']);
        });

        // Full Text Index
        DB::statement('ALTER TABLE categories ADD FULLTEXT fulltext_index (name)');

        Schema::create('categories_closure', function (Blueprint $table) {
            $table->bigIncrements('closure_id');

            $table->bigInteger('ancestor', false, true);
            $table->bigInteger('descendant', false, true);
            $table->integer('depth', false, true);

            $table->foreign('ancestor')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('descendant')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });

        Schema::create('categories_models', function (Blueprint $table) {
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->index(['model_type', 'model_id']);

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->primary(
                ['category_id', 'model_type', 'model_id'],
                'categorables_primary'
            );
        });
    }

    public function down()
    {
        Schema::table('categories_closure', function (Blueprint $table) {
            Schema::dropIfExists('categories_closure');
        });

        Schema::table('categories', function (Blueprint $table) {
            Schema::dropIfExists('categories');
        });

        Schema::dropIfExists('categories_models');
    }
}
