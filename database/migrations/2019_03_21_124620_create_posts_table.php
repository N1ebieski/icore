<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->unsignedInteger('user_id')->index();
            $table->string('title');
            $table->longText('content_html');
            $table->longText('content');
            $table->string('seo_title')->nullable();
            $table->text('seo_desc')->nullable();
            $table->boolean('seo_noindex')->default(0);
            $table->boolean('seo_nofollow')->default(0);
            $table->integer('status')->unsigned();
            $table->boolean('comment')->default(0);            
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Full Text Index
        DB::statement('ALTER TABLE posts ADD FULLTEXT fulltext_index (title, content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
