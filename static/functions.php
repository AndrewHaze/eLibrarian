<?php

function set_cors()
{
    // Allow from any origin
    if (isset($_SERVER["HTTP_ORIGIN"])) {
        // Decide if the origin in $_SERVER["HTTP_ORIGIN"] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400"); // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        // may also be using PUT, PATCH, HEAD etc
        {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        }

        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
            header("Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");
        }

        exit(0);
    }
}

function pdo_connect()
{
    $host = "127.0.0.1";
    $db = "elib";
    $user = "root";
    $pass = "";
    $charset = "utf8";

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return $pdo = new PDO($dsn, $user, $pass, $opt);
}

function clear_dir($dir)
{
    $res = false;
    if (file_exists($dir . '/')) {
        $files = glob($dir . '/' . $_SESSION['user'] . '*');
        if (count($files) > 0) {
            foreach ($files as $file) {
                $res = unlink($file);
            }
        } else {
            return true;
        }

    }
    return $res;
}
