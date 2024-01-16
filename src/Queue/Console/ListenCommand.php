<?php

namespace Gecche\Multidomain\Queue\Console;

use Gecche\Multidomain\Queue\ListenerOptions;
use Illuminate\Queue\Console\ListenCommand as BaseListenCommand;

/**
 * Class ListenCommand
 *
 * @package Gecche\Multidomain\Queue\Console
 */
class ListenCommand extends BaseListenCommand
{
    /**
     * Get the listener options for the command.
     *
     * @return ListenerOptions
     */
    protected function gatherOptions()
    {
        $backoff = (($this->hasOption('backoff')) ? $this->option('backoff') : $this->option('delay'));

        return new ListenerOptions(
            name: $this->option('name'),
            environment: $this->option('env'),
            backoff: $backoff,
            memory: $this->option('memory'),
            timeout: $this->option('timeout'),
            sleep: $this->option('sleep'),
            rest: $this->option('rest'),
            maxTries: $this->option('tries'),
            force: $this->option('force'),
            domain: $this->option('domain')
        );
    }

}
