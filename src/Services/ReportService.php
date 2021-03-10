<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use Illuminate\Contracts\Auth\Guard as Auth;

/**
 * [ReportService description]
 */
class ReportService implements Creatable
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
     * Undocumented function
     *
     * @param Report $report
     * @param Auth $auth
     */
    public function __construct(Report $report, Auth $auth)
    {
        $this->report = $report;

        $this->auth = $auth;
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param  array  $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->report->user()->associate($this->auth->user());
        $this->report->morph()->associate($this->report->morph);

        $this->report->content = $attributes['content'];

        $this->report->save();

        return $this->report;
    }
}
