<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->bigInteger('model_id')->unsigned();
            $table->string('model_type');
            $table->bigInteger('parent_id')->unsigned()->index()->nullable();
            $table->text('content_html');
            $table->text('content');
            $table->tinyInteger('status')->unsigned();
            $table->boolean('censored')->unsigned()->default(false);
            $table->integer('position', false, true);
            $table->integer('real_depth', false, true);
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['model_type', 'model_id', 'user_id']);

            $table->foreign('parent_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');
        });

        // Full Text Index
        DB::statement('ALTER TABLE comments ADD FULLTEXT fulltext_index (content)');

        Schema::create('comments_closure', function (Blueprint $table) {
            $table->bigIncrements('closure_id');

            $table->bigInteger('ancestor', false, true);
            $table->bigInteger('descendant', false, true);
            $table->integer('depth', false, true);

            $table->foreign('ancestor')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');

            $table->foreign('descendant')
                ->references('id')
                ->on('comments')
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
        Schema::dropIfExists('comments');
        Schema::dropIfExists('comments_closure');
    }
}
