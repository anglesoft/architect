<?php

namespace Angle\Architect\Traits;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;

trait ImplementsFeatures
{
    use Injection;
    use DispatchesJobs;

    /**
     * Implement the given feature injecting the given arguments.
     *
     * @param string $feature
     * @param array  $arguments
     * @return mixed
     */
    public function feature($feature, $arguments = [])
    {
        return $this->dispatch(
            $this->inject($feature, new Collection(), $arguments)
        );
    }

    /**
     * Alias to feature method
     *
     * @param string $feature
     * @param array  $arguments
     * @return mixed
     */
    public function implement($feature, $arguments = [])
    {
        return $this->feature($feature, $arguments);
    }
}
