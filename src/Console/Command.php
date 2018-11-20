<?php

namespace Angle\Architect\Console;

use Angle\Architect\Database\SprintRepository as Repository;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Console\ConfirmableTrait;

class Command extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * Checks if the package is installed.
     *
     * @return bool
     */
    protected function installed() : bool
    {
        return (new Repository)->repositoryExists();
    }

    /**
     * Runs the installer.
     *
     * @return void
     */
    protected function ensureIsInstalled() : void
    {
        if ( ! $this->installed()) {
            $this->call('architect:install');
        }
    }
}
