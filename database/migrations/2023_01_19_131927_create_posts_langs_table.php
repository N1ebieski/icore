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

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use N1ebieski\ICore\Models\PostLang\PostLang;

// phpcs:ignore
class CreatePostsLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $postLang = new PostLang();
        $post = new Post();

        Schema::create($postLang->getTable(), function (Blueprint $table) use ($post) {
            $table->bigIncrements('id');
            $table->bigInteger('post_id')->unsigned()->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_desc')->nullable();
            $table->integer('progress')->default(0);
            $table->string('lang', 2)->index();
            $table->timestamp('translated_at')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'lang']);

            $table->foreign('post_id')
                ->references('id')
                ->on($post->getTable())
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE `{$postLang->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$postLang->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$postLang->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$postLang->getTable()}`");

        $lang = Config::get('app.locale');

        DB::statement("
            INSERT INTO `{$postLang->getTable()}` (
                `post_id`, `slug`, `title`, `content_html`, `content`, `seo_title`, `seo_desc`, `progress`, `lang`, `created_at`, `updated_at`
            ) (
                SELECT `id`, `slug`, `title`, `content_html`, `content`, `seo_title`, `seo_desc`, 100, '{$lang}', `created_at`, `updated_at`
                FROM `{$post->getTable()}`
            )
        ");

        Schema::table($post->getTable(), function (Blueprint $table) {
            $table->dropColumn(['slug', 'title', 'content_html', 'content', 'seo_title', 'seo_desc']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $postLang = new PostLang();
        $post = new Post();

        Schema::table($post->getTable(), function (Blueprint $table) {
            $table->string('slug')->after('id');
            $table->string('title')->after('user_id');
            $table->longText('content_html')->nullable()->after('title');
            $table->longText('content')->nullable()->after('content_html');
            $table->string('seo_title')->nullable()->after('content');
            $table->text('seo_desc')->nullable()->after('seo_title');
        });

        DB::statement("
            UPDATE `{$post->getTable()}`
            INNER JOIN `{$postLang->getTable()}` ON `{$post->getTable()}`.`id` = `{$postLang->getTable()}`.`post_id`
            SET            
                `{$post->getTable()}`.`slug` = `{$postLang->getTable()}`.`slug`,
                `{$post->getTable()}`.`title` = `{$postLang->getTable()}`.`title`,
                `{$post->getTable()}`.`content_html` = `{$postLang->getTable()}`.`content_html`,
                `{$post->getTable()}`.`content` = `{$postLang->getTable()}`.`content`,
                `{$post->getTable()}`.`seo_title` = `{$postLang->getTable()}`.`seo_title`,
                `{$post->getTable()}`.`seo_desc` = `{$postLang->getTable()}`.`seo_desc`                
            WHERE `{$post->getTable()}`.`id` = `{$postLang->getTable()}`.`post_id`
        ");

        Schema::table($post->getTable(), function (Blueprint $table) {
            $table->unique('slug');
        });

        DB::statement("ALTER TABLE `{$post->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$post->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$post->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$post->getTable()}`");

        Schema::dropIfExists($postLang->getTable());
    }
}
