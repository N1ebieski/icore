<?php

namespace N1ebieski\ICore\Http\Clients\Intelekt\Post\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use Illuminate\Support\Collection as Collect;

class IndexResponse extends Response
{
    /**
     * Undocumented variable
     *
     * @var Collect
     */
    protected $collect;

    /**
     * Undocumented function
     *
     * @param object $parameters
     * @param Collect $collect
     */
    public function __construct(object $parameters, Collect $collect)
    {
        $this->collect = $collect;

        parent::__construct($parameters);
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return self
     */
    protected function setData(array $data)
    {
        $this->parameters->data = $this->collect->make($data);

        return $this;
    }
}
