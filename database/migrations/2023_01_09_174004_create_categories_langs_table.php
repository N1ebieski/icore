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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Models\CategoryLang\CategoryLang;

// phpcs:ignore
class CreateCategoriesLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categoryLang = new CategoryLang();
        $category = new Category();

        Schema::create($categoryLang->getTable(), function (Blueprint $table) use ($category) {
            $table->bigIncrements('id');
            $table->bigInteger('category_id')->unsigned()->index();
            $table->string('slug')->unique();
            $table->string('name');
            $table->integer('progress')->default(0);
            $table->string('lang')->index();
            $table->timestamps();

            $table->unique(['category_id', 'lang']);

            $table->foreign('category_id')
                ->references('id')
                ->on($category->getTable())
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE `{$categoryLang->getTable()}` ADD FULLTEXT `fulltext_index` (`name`)");

        DB::statement("OPTIMIZE TABLE `{$categoryLang->getTable()}`");

        $lang = Config::get('app.locale');

        DB::statement("
            INSERT INTO `{$categoryLang->getTable()}` (
                `category_id`, `slug`, `name`, `progress`, `lang`, `created_at`, `updated_at`
            ) (
                SELECT `id`, `slug`, `name`, 100, '{$lang}', `created_at`, `updated_at`
                FROM `{$category->getTable()}`
            )
        ");

        Schema::table($category->getTable(), function (Blueprint $table) {
            $table->dropColumn(['name', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $categoryLang = new CategoryLang();
        $category = new Category();

        Schema::table($category->getTable(), function (Blueprint $table) {
            $table->string('name')->after('icon');
            $table->string('slug')->after('model_type');
        });

        DB::statement("
            UPDATE `{$category->getTable()}`
            INNER JOIN `{$categoryLang->getTable()}` ON `{$category->getTable()}`.`id` = `{$categoryLang->getTable()}`.`category_id`
            SET            
                `{$category->getTable()}`.`name` = `{$categoryLang->getTable()}`.`name`,
                `{$category->getTable()}`.`slug` = `{$categoryLang->getTable()}`.`slug`
            WHERE `{$category->getTable()}`.`id` = `{$categoryLang->getTable()}`.`category_id`
        ");

        Schema::table($category->getTable(), function (Blueprint $table) {
            $table->unique('slug');
        });

        DB::statement("ALTER TABLE `{$category->getTable()}` ADD FULLTEXT `fulltext_index` (`name`)");

        DB::statement("OPTIMIZE TABLE `{$category->getTable()}`");

        Schema::dropIfExists($categoryLang->getTable());
    }
}
