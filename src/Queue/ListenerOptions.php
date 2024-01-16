<?php

namespace Gecche\Multidomain\Queue;

use Illuminate\Queue\ListenerOptions as BaseListenerOptions;

/**
 * Class ListenerOptions
 *
 * @package Gecche\Multidomain\Queue
 */
class ListenerOptions extends BaseListenerOptions
{
    /**
     * The domain the workers should run under.
     *
     * @var string|null
     */
    public ?string $domain;

    /**
     * ListenerOptions constructor.
     *
     * @param string $name
     * @param  string|null  $environment
     * @param  int|int[]  $backoff
     * @param int $memory
     * @param int $timeout
     * @param int $sleep
     * @param int $maxTries
     * @param bool $force
     * @param  int  $rest
     * @param string|null $domain
     * @return void
     */
    public function __construct($name = 'default', $environment = null, $backoff = 0, $memory = 128, $timeout = 60, $sleep = 3, $maxTries = 1, $force = false, $rest = 0, ?string $domain = null)
    {
        $this->domain = $domain;

        parent::__construct($name, $environment, $backoff, $memory, $timeout, $sleep, $maxTries, $force, $rest);
    }
}
