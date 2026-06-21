<?php

if (!function_exists('sqlsrv_connect')) {
    function sqlsrv_connect($serverName, $connectionInfo) {
        return null; // or resource
    }
}

if (!function_exists('sqlsrv_close')) {
    function sqlsrv_close($conn) {
        return true;
    }
}

if (!function_exists('sqlsrv_errors')) {
    function sqlsrv_errors() {
        return [];
    }
}

if (!function_exists('sqlsrv_query')) {
    function sqlsrv_query($conn, $sql, $params = null, $options = null) {
        return null; // statement resource
    }
}

if (!function_exists('sqlsrv_fetch_array')) {
    function sqlsrv_fetch_array($stmt, $fetchType = null, $row = null, $offset = null) {
        return null; // array|false
    }
}

if (!function_exists('sqlsrv_prepare')) {
    function sqlsrv_prepare($conn, $sql, $params = null, $options = null) {
        return null;
    }
}

if (!function_exists('sqlsrv_execute')) {
    function sqlsrv_execute($stmt) {
        return true;
    }
}