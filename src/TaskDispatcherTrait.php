<?php

namespace Angle\Architect;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use ReflectionClass;

// TODO move to Traits\Taskable | RunsTasks and rename methods

trait TaskDispatcherTrait
{
    /**
     * beautifier function to be called instead of the
     * laravel function dispatchFromArray.
     * When the $arguments is an instance of Request
     * it will call dispatchFrom instead.
     *
     * @param string                         $task
     * @param array|\Illuminate\Http\Request $arguments
     * @param array                          $extra
     *
     * @return mixed
     */
    public function run($task, $arguments = [], $extra = [])
    {
        if ($arguments instanceof Request) {
            $result = $this->dispatch($this->marshal($task, $arguments, $extra));
        } else {
            if (!is_object($task)) {
                $task = $this->marshal($task, new Collection(), $arguments);
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
     *
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
