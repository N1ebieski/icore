<?php

namespace N1ebieski\ICore\Services;

use N1ebieski\ICore\Models\Report\Report;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\Interfaces\Creatable;

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
     * [__construct description]
     * @param Report $report [description]
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param  array  $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->report->user()->associate(auth()->user()->id);
        $this->report->morph()->associate($this->report->getMorph());

        $this->report->content = $attributes['content'];

        $this->report->save();

        return $this->report;
    }
}
