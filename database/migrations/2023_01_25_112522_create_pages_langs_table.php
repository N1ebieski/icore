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

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use N1ebieski\ICore\Models\PageLang\PageLang;

// phpcs:ignore
class CreatePagesLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pageLang = new PageLang();
        $page = new Page();

        Schema::create($pageLang->getTable(), function (Blueprint $table) use ($page) {
            $table->bigIncrements('id');
            $table->bigInteger('page_id')->unsigned()->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_desc')->nullable();
            $table->integer('progress')->default(0);
            $table->string('lang', 2)->index();
            $table->timestamps();

            $table->unique(['page_id', 'lang']);

            $table->foreign('page_id')
                ->references('id')
                ->on($page->getTable())
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE `{$pageLang->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$pageLang->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$pageLang->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$pageLang->getTable()}`");

        $lang = Config::get('app.locale');

        DB::statement("
            INSERT INTO `{$pageLang->getTable()}` (
                `page_id`, `slug`, `title`, `content_html`, `content`, `seo_title`, `seo_desc`, `progress`, `lang`, `created_at`, `updated_at`
            ) (
                SELECT `id`, `slug`, `title`, `content_html`, `content`, `seo_title`, `seo_desc`, 100, '{$lang}', `created_at`, `updated_at`
                FROM `{$page->getTable()}`
            )
        ");

        Schema::table($page->getTable(), function (Blueprint $table) {
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
        $pageLang = new PageLang();
        $page = new Page();

        Schema::table($page->getTable(), function (Blueprint $table) {
            $table->string('slug')->after('id');
            $table->string('title')->after('icon');
            $table->longText('content_html')->nullable()->after('title');
            $table->longText('content')->nullable()->after('content_html');
            $table->string('seo_title')->nullable()->after('content');
            $table->text('seo_desc')->nullable()->after('seo_title');
        });

        DB::statement("
            UPDATE `{$page->getTable()}`
            INNER JOIN `{$pageLang->getTable()}` ON `{$page->getTable()}`.`id` = `{$pageLang->getTable()}`.`page_id`
            SET            
                `{$page->getTable()}`.`slug` = `{$pageLang->getTable()}`.`slug`,
                `{$page->getTable()}`.`title` = `{$pageLang->getTable()}`.`title`,
                `{$page->getTable()}`.`content_html` = `{$pageLang->getTable()}`.`content_html`,
                `{$page->getTable()}`.`content` = `{$pageLang->getTable()}`.`content`,
                `{$page->getTable()}`.`seo_title` = `{$pageLang->getTable()}`.`seo_title`,
                `{$page->getTable()}`.`seo_desc` = `{$pageLang->getTable()}`.`seo_desc`                
            WHERE `{$page->getTable()}`.`id` = `{$pageLang->getTable()}`.`page_id`
        ");

        Schema::table($page->getTable(), function (Blueprint $table) {
            $table->unique('slug');
        });

        DB::statement("ALTER TABLE `{$page->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$page->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$page->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$page->getTable()}`");

        Schema::dropIfExists($pageLang->getTable());
    }
}
