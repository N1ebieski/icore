<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_desc')->nullable();
            $table->boolean('seo_noindex')->default(0);
            $table->boolean('seo_nofollow')->default(0);
            $table->tinyInteger('status')->unsigned();
            $table->boolean('comment')->default(0);
            $table->bigInteger('parent_id')->unsigned()->index()->nullable();
            $table->integer('position', false, true);
            $table->integer('real_depth', false, true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade');
        });

        // Full Text Index
        DB::statement('ALTER TABLE pages ADD FULLTEXT fulltext_index (title, content)');

        Schema::create('pages_closure', function (Blueprint $table) {
            $table->bigIncrements('closure_id');

            $table->bigInteger('ancestor', false, true);
            $table->bigInteger('descendant', false, true);
            $table->integer('depth', false, true);

            $table->foreign('ancestor')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade');

            $table->foreign('descendant')
                ->references('id')
                ->on('pages')
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
        Schema::dropIfExists('pages_closure');

        Schema::dropIfExists('pages');
    }
}
