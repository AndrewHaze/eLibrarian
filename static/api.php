<?php

$chars = array(
    array(
        "text" => "*",
        "value" => "*",
    ),
    array(
        "text" => "А",
        "value" => "А",
    ),
    array(
        "text" => "Б",
        "value" => "Б",
    ),
    array(
        "text" => "П",
        "value" => "П",
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
        "books" => 15,
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

set_cors();

$res = array("data" => array(), "success" => true, "error" => "");

$_POST = json_decode(file_get_contents("php://input"), true);

if (isset($_POST["cmd"])) {
    switch ($_POST["cmd"]) {
        case "first":
            $pdo = pdo_connect();
            if ($pdo) {
                $stmt = $pdo->query("SELECT COUNT(*) FROM users;");
                $count = $stmt->fetchColumn();
            }
            if ($count > 0) {
                $res["data"] = false;
            } else {
                $res["data"] = true;
            }
            break;
        case "exist":
            $pdo = pdo_connect();
            if ($pdo) {
                $username = $_POST["username"];
                $stmt = $dbh->prepare('SELECT COUNT(*) FROM users WHERE users=:login');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    $res["data"] = false;
                } else {
                    $res["data"] = true;
                }
            }
            break;
        case "с_list":
            $res["data"] = $chars;
            break;
        case "a_list":
            $res["data"] = $authors;
            break;
        default:
            $res["success"] = false;
            $res["error"] = "Unknown command";
    }
} else {
    $res["success"] = false;
    $res["error"] = "No command";
}

echo json_encode($res);
