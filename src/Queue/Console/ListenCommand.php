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

        echo $this->option('name') . "\n";
            echo $this->option('env') . "\n";
        echo $backoff . "\n";
            echo $this->option('memory') . "\n";
            echo $this->option('timeout') . "\n";
            echo $this->option('sleep') . "\n";
            echo $this->option('tries') . "\n";
            echo $this->option('force') . "\n";
            echo $this->option('domain') . "\n";


        return new ListenerOptions(
            $this->option('name'),
            $this->option('env'),
            $backoff,
            $this->option('memory'),
            $this->option('timeout'),
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('force'),
            $this->option('domain')
        );
    }

}
