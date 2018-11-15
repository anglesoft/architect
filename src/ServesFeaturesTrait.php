<?php

namespace Angle\Architect;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

// TODO move to Traits\Implement and rename serve() to implement()
// https://www.thesaurus.com/browse/accomplish

trait ServesFeaturesTrait
{
    use MarshalTrait;
    use DispatchesJobs;

    /**
     * Serve the given feature with the given arguments.
     *
     * @param string $feature
     * @param array  $arguments
     *
     * @return mixed
     */
    public function serve($feature, $arguments = [])
    {
        return $this->dispatch(
            $this->marshal($feature, new Collection(), $arguments)
        );
    }
}
