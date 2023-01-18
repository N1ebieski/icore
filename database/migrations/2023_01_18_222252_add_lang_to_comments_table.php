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
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class AddLangToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $comment = new Comment();

        Schema::table($comment->getTable(), function (Blueprint $table) {
            $table->string('lang')->index()->after('real_depth');

            $table->index(['model_type', 'model_id', 'lang']);
            $table->index(['model_type', 'model_id', 'user_id', 'lang']);
        });

        $lang = Config::get('app.locale');

        DB::statement("UPDATE `{$comment->getTable()}` SET `{$comment->getTable()}`.`lang` = '{$lang}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $comment = new Comment();

        Schema::table($comment->getTable(), function (Blueprint $table) {
            $table->dropColumn('lang');

            $table->dropIndex('comments_model_type_model_id_lang_index');
            $table->dropIndex('comments_model_type_model_id_user_id_lang_index');
        });
    }
}
