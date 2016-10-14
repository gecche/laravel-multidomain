<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('storage_temp_path')) {

    /**
     * Get the path to the storage folder of the domain.
     *
     * @param   string  $path
     * @return  string
     */
    function storage_temp_path($path = '') {
        $id = Auth::id();
        if (!$id) {
            $id = 0;
        }

        return app('path.storage') . DIRECTORY_SEPARATOR . "files" .
                DIRECTORY_SEPARATOR . "temp/user_" . $id . ($path ? '/' . $path : $path);
    }

}


if (!function_exists('domain_sanitized')) {

    /**
     * Generate a URL to a controller action.
     *
     * @param  string  $name
     * @param  array   $parameters
     * @return string
     */
    function domain_sanitized($domain) {
        return str_replace('.', '_', $domain);
    }

}

if (!function_exists('domain_root_url')) {

    /**
     * Generate a URL to a controller action.
     *
     * @param  string  $name
     * @param  array   $parameters
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
