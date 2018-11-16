<?php

namespace Angle\Architect\Traits;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ReflectionClass;

trait Taskable
{
    /**
     * Beautifier function to be called instead of the laravel function dispatchFromArray.
     * When the $arguments is an instance of Request it will call dispatchFrom instead.
     *
     * @param string                         $task
     * @param array|\Illuminate\Http\Request $arguments
     * @param array                          $extra
     * @return mixed
     */
    public function task($task, $arguments = [], $extra = [])
    {
        if ($arguments instanceof Request) {
            $result = $this->dispatch($this->inject($task, $arguments, $extra));
        } else {
            if (!is_object($task)) {
                $task = $this->inject($task, new Collection(), $arguments);
            }

            $result = $this->dispatch($task, $arguments);
        }

        return $result;
    }

    /**
     * Run the given job in the given queue.
     *
     * @param string     $task
     * @param array      $arguments
     * @param Queue|null $queue
     * @return mixed
     */
    public function runInQueue($task, array $arguments = [], $queue = 'default')
    {
        // instantiate and queue the job
        $reflection = new ReflectionClass($task);
        $taskInstance = $reflection->newInstanceArgs($arguments);
        $taskInstance->onQueue((string) $queue);

        return $this->dispatch($taskInstance);
    }
}
