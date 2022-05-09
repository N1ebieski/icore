<?php

namespace N1ebieski\ICore\Services\Report;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;

class ReportService implements CreateInterface
{
    /**
     * [private description]
     * @var Report
     */
    protected $report;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Report $report
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(Report $report, Auth $auth, DB $db)
    {
        $this->report = $report;

        $this->auth = $auth;
        $this->db = $db;
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param  array  $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->report->user()->associate($this->auth->user());
            $this->report->morph()->associate($this->report->morph);

            $this->report->content = $attributes['content'];

            $this->report->save();

            return $this->report;
        });
    }
}
