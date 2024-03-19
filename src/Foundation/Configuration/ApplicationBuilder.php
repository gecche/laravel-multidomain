<?php

namespace Gecche\Multidomain\Foundation\Configuration;

class ApplicationBuilder extends \Illuminate\Foundation\Configuration\ApplicationBuilder
{


    /**
     * Register the standard kernel classes for the application.
     *
     * @return $this
     */
    public function withKernels()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \Gecche\Multidomain\Foundation\Http\Kernel::class,
        );

        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \Gecche\Multidomain\Foundation\Console\Kernel::class,
        );

        return $this;
    }

}
