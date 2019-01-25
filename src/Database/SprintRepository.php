<?php

namespace Angle\Architect\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class SprintRepository
{
    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the sprint table.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new database sprint repository instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  string  $table
     * @return void
     */
    public function __construct(string $table = null, string $connection = null)
    {
        $this->table = $table != null ? $table : config('architect.database.table');
        $this->connection = $connection != null ? $connection : config('architect.database.connection');
        $this->resolver = app('db');
    }

    /**
     * Create a new sprint entry in the database.
     *
     * @param string $sprint
     * @return bool
     */
    public function create(string $sprint, int $batch = null) : bool
    {
        return $this->table()
            ->insert([
                'sprint' => $sprint,
                'batch' => $batch ?? $this->getNextBatchNumber()
            ]);
    }

    /**
     * Get the completed sprints.
     *
     * @return array
     */
    public function getRan()
    {
        return $this->table()
            ->orderBy('batch', 'asc')
            ->orderBy('sprint', 'asc')
            ->pluck('sprint')->all();
    }

    /**
     * Check if sprint was run
     * @param  string  $sprint Sprint name
     * @return boolean
     */
    public function hasRun(string $sprint)
    {
        return in_array($sprint, $this->getRan());
    }

    /**
     * Get list of sprints.
     *
     * @param  int  $steps
     * @return array
     */
    public function getSprints() // (int $steps = 0)
    {
        $query = $this->table()->where('batch', '>=', '1');

        return $query->orderBy('batch', 'desc')
            ->orderBy('sprint', 'desc')
            // ->take($steps)
            ->get()->all();
    }

    /**
     * Fetches the sprints to rollback
     *
     * @return Support\Illuminate\Collection
     */
    public function getSprintsToRollback() : Collection
    {
        return $this->getSprintsByBatch(
            $this->getLastBatchNumber()
        );
    }

    /**
     * Get the last sprint batch.
     *
     * @return array
     */
    public function getLast()
    {
        $query = $this->table()->where('batch', $this->getLastBatchNumber());

        return $query->orderBy('sprint', 'desc')->get()->all();
    }

    /**
     * Get the completed sprints with their batch numbers.
     *
     * @return array
     */
    public function getSprintBatches()
    {
        return $this->table()
            ->orderBy('batch', 'asc')
            ->orderBy('sprint', 'asc')
            ->pluck('batch', 'sprint')->all();
    }

    /**
     * Fetches sprints by batch number
     * @param  int  $batch
     * @return Illuminate\Support\Collection
     */
    public function getSprintsByBatch(int $batch = null) : Collection
    {
        return $this->table()
            ->orderBy('batch', 'asc')
            ->where('batch', $batch ?? $this->getLastBatchNumber())
            ->get();
    }

    /**
     * Log that a sprint was run.
     *
     * @param  string  $file
     * @param  int  $batch
     * @return void
     */
    public function log($file, $batch)
    {
        $record = ['sprint' => $file, 'batch' => $batch];

        $this->table()->insert($record);
    }

    /**
     * Remove a sprint from the log.
     *
     * @param  object  $record
     * @return void
     */
    public function delete($record) : void
    {
        $this->table()->where('sprint', $record->sprint)->delete();
    }

    /**
     * Remove a sprint by its name.
     *
     * @param  string  $sprint
     * @return void
     */
    public function deleteByName(string $sprint) : void
    {
        $this->table()->where('sprint', $sprint)->delete();
    }

    /**
     * Get the next sprint batch number.
     *
     * @return int
     */
    public function getNextBatchNumber() : int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the last sprint batch number.
     *
     * @return int
     */
    public function getLastBatchNumber() : int
    {
        return $this->table()->max('batch') ?? 0;
    }

    /**
     * Create the sprint repository data store.
     *
     * @return void
     */
    public function createRepository() : void
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        $schema->create($this->table, function ($table) {
            // The sprints table is responsible for keeping track of which of the
            // sprints have actually run for the application. We'll create the
            // table to hold the sprint file's path as well as the batch ID.
            $table->increments('id');
            $table->string('sprint');
            $table->integer('batch');
        });
    }

    /**
     * Determine if the sprint repository exists.
     *
     * @return bool
     */
    public function repositoryExists() : bool
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }

    /**
     * Get a query builder for the sprint table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table() : Builder
    {
        return $this->getConnection()->table($this->table)->useWritePdo();
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver() : ConnectionResolverInterface
    {
        return $this->resolver;
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection() : Connection
    {
        return $this->resolver->connection($this->connection);
    }

    /**
     * Set the information source to gather data.
     *
     * @param  string  $name
     * @return void
     */
    public function setSource(string $name) : void
    {
        $this->connection = $name;
    }

    /**
     * Set the database table.
     *
     * @param  string  $name
     * @return void
     */
    public function setTable(string $name) : void
    {
        $this->table = $name;
    }
}
