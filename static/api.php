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

require_once('functions.php'); 

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
                if ($count > 0) {
                    $res["data"] = false;
                } else {
                    $res["data"] = true;
                }
            } else {
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "exist":
            $pdo = pdo_connect();
            if ($pdo) {
                $username = $_POST["dat"];
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE login = :login');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    $res["data"] = false;
                } else {
                    $res["data"] = true;
                }
            } else {
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "register":
            $pdo = pdo_connect();
            if ($pdo) {
                $username = $_POST["usr"];
                $password = $_POST["psw"];
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO `users` (`id`, `login`, `hash`) VALUES (NULL, :login, :hash);');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->bindValue(':hash', $hash, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "verification":
            $pdo = pdo_connect();
            if ($pdo) {
                $username = $_POST["usr"];
                $password = $_POST["psw"];
                $stmt = $pdo->prepare('SELECT hash FROM users WHERE login = :login ');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->execute();
                $hash = $stmt->fetchColumn();
                if (password_verify ($password , $hash)) {
                    $res["data"] = true;
                } else {
                    $res["data"] = false;
                }
            } else {
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "test":
            $res["data"] = $_POST["dat"];
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
