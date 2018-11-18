<?php

namespace Angle\Architect\Traits;

use ArrayAccess;
use Exception;
use ReflectionClass;
use ReflectionParameter;

trait Injection
{
    /**
     * Inject a command from the given array accessible object.
     *
     * @param string       $command
     * @param \ArrayAccess $source
     * @param array        $extras
     *
     * @return mixed
     */
    protected function inject($command, ArrayAccess $source, array $extras = [])
    {
        $injected = [];

        $reflection = new ReflectionClass($command);

        if ($constructor = $reflection->getConstructor()) {
            $injected = array_map(function ($parameter) use ($command, $source, $extras) {
                return $this->getParameterValueForCommand($command, $source, $parameter, $extras);
            }, $constructor->getParameters());
        }

        return $reflection->newInstanceArgs($injected);
    }

    /**
     * Get a parameter value for an injected command.
     *
     * @param string               $command
     * @param \ArrayAccess         $source
     * @param \ReflectionParameter $parameter
     * @param array                $extras
     *
     * @return mixed
     */
    protected function getParameterValueForCommand($command, ArrayAccess $source,
        ReflectionParameter $parameter, array $extras = [])
    {
        if (array_key_exists($parameter->name, $extras)) {
            return $extras[$parameter->name];
        }

        if (isset($source[$parameter->name])) {
            return $source[$parameter->name];
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception("Unable to map parameter [{$parameter->name}] to command [{$command}]");
    }
}
