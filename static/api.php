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
    session_name("xeg6joZqNSGP3FyEi6xW");
    session_start(); 
    $pdo = pdo_connect();
    switch ($_POST["cmd"]) {
        case "first": //есть заг. пользователи?
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
        case "exist": //есть такой пользователь?
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
        case "register": //регистрация
            if ($pdo) {
                $username = $_POST["usr"];
                $password = $_POST["psw"];
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO `users` (`id`, `login`, `hash`) VALUES (NULL, :login, :hash);');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->bindValue(':hash', $hash, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['user'] = $username;
                $res["data"] = $_SESSION['user'];
            } else {
                $_SESSION['user'] = null;
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "verification": //логин
            if ($pdo) {
                $username = $_POST["usr"];
                $password = $_POST["psw"];
                $stmt = $pdo->prepare('SELECT hash FROM users WHERE login = :login ');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->execute();
                $hash = $stmt->fetchColumn();
                if (password_verify ($password , $hash)) {
                    $_SESSION['user'] = $username;
                    $res["data"] = $_SESSION['user'];
                } else {
                    $_SESSION['user'] = null;
                    $res["data"] = false;
                    $res["success"] = false;
                    $res["error"] = "Wrong Password";
                }
            } else {
                $_SESSION['user'] = null;
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
        case "clear_upload": //очистка папки uploads, для текщей сессии
            $res["data"] = clear_dir('uploads');
            if (!$res["data"]) {
                $res["success"] = false;
                $res["error"] = "I/O Error (Clear Upload)";
            }
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
