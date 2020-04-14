<?php


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('domain_sanitized')) {

    /**
     * Replace dots in a domain name with another character (underscores by default)
     *
     * @param  string  $domain
     * @return string
     */
    function domain_sanitized($domain,$replacing_char = '_') {
        return str_replace('.', $replacing_char, $domain);
    }

}

if (!function_exists('domain_root_url')) {

    /**
     * Get the root url of the current http(s) domain of the application.
     * It adds the $path if passed
     *
     * @param  string  $path
     * @return string
     */
    function domain_root_url($path = null) {
        $domain = app('domain');
        $scheme = app('domain_scheme') . '://';
        $port = ':' . app('domain_port');
        if (in_array($port,[':80',':443']))
            $port = '';
        if ($path)
            return $scheme . $domain . $port . $path;
        return $scheme . $domain . $port;
    }

}

if (! function_exists('env_path')) {
    /**
     * Get the path to the env files.
     *
     * @param  string  $path
     * @return string
     */
    function env_path($path = '')
    {
        return app()->environmentPath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

