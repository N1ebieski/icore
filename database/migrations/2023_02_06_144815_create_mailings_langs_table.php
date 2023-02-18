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

use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use N1ebieski\ICore\Models\MailingLang\MailingLang;

// phpcs:ignore
class CreateMailingsLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mailingLang = new MailingLang();
        $mailing = new Mailing();

        Schema::create($mailingLang->getTable(), function (Blueprint $table) use ($mailing) {
            $table->bigIncrements('id');
            $table->bigInteger('mailing_id')->unsigned()->index();
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->longText('content')->nullable();
            $table->integer('progress')->default(0);
            $table->string('lang', 2)->index();
            $table->timestamp('translated_at')->nullable();
            $table->timestamps();

            $table->unique(['mailing_id', 'lang']);

            $table->foreign('mailing_id')
                ->references('id')
                ->on($mailing->getTable())
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE `{$mailingLang->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$mailingLang->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$mailingLang->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$mailingLang->getTable()}`");

        $lang = Config::get('app.locale');

        DB::statement("
            INSERT INTO `{$mailingLang->getTable()}` (
                `mailing_id`, `title`, `content_html`, `content`, `progress`, `lang`, `created_at`, `updated_at`
            ) (
                SELECT `id`, `title`, `content_html`, `content`, 100, '{$lang}', `created_at`, `updated_at`
                FROM `{$mailing->getTable()}`
            )
        ");

        Schema::table($mailing->getTable(), function (Blueprint $table) {
            $table->dropColumn(['title', 'content_html', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $mailingLang = new MailingLang();
        $mailing = new Mailing();

        Schema::table($mailing->getTable(), function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->longText('content_html')->nullable()->after('title');
            $table->longText('content')->nullable()->after('content_html');
        });

        DB::statement("
            UPDATE `{$mailing->getTable()}`
            INNER JOIN `{$mailingLang->getTable()}` ON `{$mailing->getTable()}`.`id` = `{$mailingLang->getTable()}`.`mailing_id`
            SET            
                `{$mailing->getTable()}`.`title` = `{$mailingLang->getTable()}`.`title`,
                `{$mailing->getTable()}`.`content_html` = `{$mailingLang->getTable()}`.`content_html`,
                `{$mailing->getTable()}`.`content` = `{$mailingLang->getTable()}`.`content`              
            WHERE `{$mailing->getTable()}`.`id` = `{$mailingLang->getTable()}`.`mailing_id`
        ");

        DB::statement("ALTER TABLE `{$mailing->getTable()}` ADD FULLTEXT `fulltext_index` (`title`, `content`)");
        DB::statement("ALTER TABLE `{$mailing->getTable()}` ADD FULLTEXT `fulltext_title` (`title`)");
        DB::statement("ALTER TABLE `{$mailing->getTable()}` ADD FULLTEXT `fulltext_content` (`content`)");

        DB::statement("OPTIMIZE TABLE `{$mailing->getTable()}`");

        Schema::dropIfExists($mailingLang->getTable());
    }
}
