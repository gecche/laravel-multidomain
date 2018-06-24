<?php

namespace Gecche\Multidomain\Queue;

class ListenerOptions extends \Illuminate\Queue\ListenerOptions
{
    /**
     * The domain the workers should run under.
     *
     * @var string
     */
    public $domain;

    /**
     * Create a new listener options instance.
     *
     * @param  string  $domain
     * @param  string  $environment
     * @param  int  $delay
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @return void
     */
    public function __construct($domain = null, $environment = null, $delay = 0, $memory = 128, $timeout = 60, $sleep = 3, $maxTries = 0, $force = false)
    {
        $this->domain = $domain;

        parent::__construct($environment, $delay, $memory, $timeout, $sleep, $maxTries, $force);
    }
}
