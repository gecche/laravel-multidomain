<?php

namespace Gecche\Multidomain\Foundation;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cookie;

class Application extends \Illuminate\Foundation\Application {
    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = null;

    /**
     * Detect the application's current environment.
     *
     * @param  array|string  $envs
     * @return string
     */
    public function detectDomain() {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $domainDetector = new DomainDetector();
        $fullDomain = $domainDetector->detect($args);
        list($domain_scheme,$domain_name,$domain_port) = $domainDetector->split($fullDomain);
        $this['full_domain'] = $fullDomain;
        $this['domain'] = $domain_name;
        $this['domain_scheme'] = $domain_scheme;
        $this['domain_port'] = $domain_port;
        return;
    }

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string
     */
    public function domain() {
        if (count(func_get_args()) > 0) {
            return in_array($this['domain'], func_get_args());
        }

        return $this['domain'];
    }

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string
     */
    public function fullDomain() {
        if (count(func_get_args()) > 0) {
            return in_array($this['full_domain'], func_get_args());
        }

        return $this['full_domain'];
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: $this->environmentFileDomain();
    }

    public function environmentFileDomain() {
        $filePath = rtrim($this['path.base'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $file = '.env.' . $this['domain'];
        return file_exists($filePath.$file) ? $file : '.env';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function domainStoragePath()
    {
        $domainPath = domain_sanitized($this['domain']);
        $domainStoragePath = $this->storagePath() . DIRECTORY_SEPARATOR . $domainPath;
        if (file_exists($domainStoragePath))
            return $domainStoragePath;
        return $this->storagePath();
    }

    public function getLocale()
    {
        $locale = $this['config']->get('app.locale');
        //SEssion $locale = $this['request']->cookie('lang') ? $this['request']->cookie('lang') : $locale;
        $locale = $this['request']->cookie('lang') ? $this['request']->cookie('lang') : $locale;
        return $locale;
    }

/**
     * Detect and set application localization environment (language).
     * NOTE: Don't foreget to ADD/SET/UPDATE the locales array in app/config/app.php!
     *
     */


//    public function configureLocale()
//    {
//
//        // Set default locale.
//        $mLocale = $this['config']->get( 'app.locale' );
//
//        // Has a session locale already been set?
//        if ( !$this['session']->has( 'locale' ) )
//        {
//            // No, a session locale hasn't been set.
//            // Was there a cookie set from a previous visit?
//            $mFromCookie = Cookie::get( 'lang', null );
//            //$mFromCookie = array_get($_COOKIE,'lang',null);
//
//            if ( $mFromCookie != null && in_array( $mFromCookie, $this['config']->get( 'app.langs' ) ) )
//            {
//                // Cookie was previously set and it's a supported locale.
//                $mLocale = $mFromCookie;
//            }
//            else
//            {
//                // No cookie was set.
//                // Attempt to get local from current URI.
//                $mFromURI = $this['request']->segment( 1 );
//                if ( $mFromURI != null && in_array( $mFromURI, $this['config']->get( 'app.langs' ) ) )
//                {
//                    // supported locale
//                    $mLocale = $mFromURI;
//                }
//                else
//                {
//                    // attempt to detect locale from browser.
//                    $mFromBrowser = substr( Request::server( 'http_accept_language' ), 0, 2 );
//                    if ( $mFromBrowser != null && in_array( $mFromBrowser, $this['config']->get( 'app.langs' ) ) )
//                    {
//                        // browser lang is supported, use it.
//                        $mLocale = $mFromBrowser;
//                    } // $mFromBrowser
//                } // $mFromURI
//            } // $mFromCookie
//
//            $this['session']->put( 'locale', $mLocale );
//
//            //$_COOKIE['lang'] = $mLocale;
//            Cookie::forever( 'lang', $mLocale);
//        } // Session?
//        else
//        {
//            // session locale is available, use it.
//            $mLocale = $this['session']->get( 'locale' );
//        } // Session?
//
//        // set application locale for current session.
//        $this->setLocale( $mLocale );
//
//    }
//
}
