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

$chars = array(
    array(
        "text" => '*',
        "value" => '*',
    ),
    array(
        "text" => 'А',
        "value" => 'А',
    ),
    array(
        "text" => 'Б',
        "value" => 'Б',
    ),
    array(
        "text" => 'П',
        "value" => 'П',
    ),
);

$authors = array(
    array(
        "id" => 1,
        "books" => 1,
        "author" => "Акунин",
        "isActive" => false,
        "_rowVariant" => "",
    ),
    array(
        "id" => 2,
        "books" => 5,
        "author" => "Бушков",
        "isActive" => false,
        "_rowVariant" => "",
    ),
    array(
        "id" => 3,
        "books" => 3,
        "author" => "Пехов",
        "isActive" => false,
        "_rowVariant" => "",
    ),
    array(
        "id" => 4,
        "books" => 12,
        "author" => "Панов",
        "isActive" => false,
        "_rowVariant" => "",
    ),
);

set_cors();

$res = array("data" => array(), "success" => true, "error" => "");

$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST["cmd"])) {
    switch ($_POST['cmd']) {
        case 'с_list':
            $res['data'] = $chars;
            break;
        case 'a_list':
            $res['data'] = $authors;
            break;
        default:
            $res['success'] = false;
            $res['error'] = 'Unknown command';
    }
} else {
    $res['success'] = false;
    $res['error'] = 'No command';
}

echo json_encode($res);
