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

namespace N1ebieski\ICore\Services\Report;

use Throwable;
use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\DatabaseManager as DB;

class ReportService
{
    /**
     * Undocumented function
     *
     * @param Report $report
     * @param DB $db
     */
    public function __construct(
        protected Report $report,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Report
     * @throws Throwable
     */
    public function create(array $attributes): Report
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->report->fill($attributes);

            $this->report->user()->associate($attributes['user']);
            $this->report->morph()->associate($attributes['morph']);

            $this->report->save();

            return $this->report;
        });
    }
}
