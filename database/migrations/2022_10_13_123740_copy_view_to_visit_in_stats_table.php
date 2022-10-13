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
use N1ebieski\ICore\Models\Stat\Stat;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class CopyViewToVisitInStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => \N1ebieski\ICore\Database\Seeders\Install\DefaultStatsSeeder::class,
            '--force' => true
        ]);

        $stat = new Stat();

        /** @var Stat */
        $statVisit = $stat->makeRepo()->firstBySlug(Slug::VISIT);

        /** @var Stat */
        $statView = $stat->makeRepo()->firstBySlug(Slug::VIEW);

        DB::statement("
            INSERT INTO `stats_values` (
                `stat_id`, `model_id`, `model_type`, `value`
            ) (
                SELECT '{$statVisit->id}', `model_id`, `model_type`, `value`
                FROM `stats_values`
                WHERE `stat_id` = {$statView->id}
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $stat = new Stat();

        /** @var Stat */
        $statVisit = $stat->makeRepo()->firstBySlug(Slug::VISIT);

        DB::statement("DELETE FROM `stats_values` WHERE `stat_id` = {$statVisit->id}");
    }
}
