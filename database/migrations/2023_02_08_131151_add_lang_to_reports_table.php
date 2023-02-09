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
use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class AddLangToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $report = new Report();

        Schema::table($report->getTable(), function (Blueprint $table) {
            $table->string('lang', 2)->index()->after('content');
        });

        $lang = Config::get('app.locale');

        DB::statement("UPDATE `{$report->getTable()}` SET `{$report->getTable()}`.`lang` = '{$lang}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $report = new Report();

        Schema::table($report->getTable(), function (Blueprint $table) {
            $table->dropColumn('lang');
        });
    }
}
