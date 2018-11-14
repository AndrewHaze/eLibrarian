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

require_once 'functions.php';

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
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE ur_login = :login');
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
                $stmt = $pdo->prepare('INSERT INTO `users` (`ur_id`, `ur_login`, `ur_hash`) VALUES (NULL, :login, :hash);');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->bindValue(':hash', $hash, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['user'] = $username;
                $res["data"] = $_SESSION['user'];
            } else {
                $_SESSION['user'] = null;
                $res["error"] = "PDO Error";
            }
            break;
        case "verification": //логин
            if ($pdo) {
                $username = $_POST["usr"];
                $password = $_POST["psw"];
                $stmt = $pdo->prepare('SELECT ur_hash FROM users WHERE ur_login = :login ');
                $stmt->bindValue(':login', $username, PDO::PARAM_INT);
                $stmt->execute();
                $hash = $stmt->fetchColumn();
                if (password_verify($password, $hash)) {
                    $_SESSION['user'] = $username;
                    $res["data"] = $_SESSION['user'];
                } else {
                    $_SESSION['user'] = null;
                    $res["data"] = false;
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
        case "proc":
            if ($pdo) {
                if ($_SESSION["user"]) {
                    $filename = 'uploads/'.$_POST["file"];
                    $fileext = pathinfo($filename, PATHINFO_EXTENSION);
                    if ($fileext == "fb2") {
                        //Создаем XML документ
                        $doc = new DOMDocument();
                        //Отключаем проверку ошибок
                        $doc->strictErrorChecking = false;
                        $doc->recover = true;
                        //Загружаем содержимое файла
                        $load = $doc->load($filename, LIBXML_NOERROR);
                        if (!$load) {
                            echo "Ошибка загрузки!";
                            $fb2error = 1;
                        }
                        //Получаем содержимое секции <description>
                        $description = $doc->getElementsByTagName('description');
                        $description = $description->item(0);
                        if (!$description) {
                            echo "No description!";
                        }
                        //Получаем название книги
                        $title_info = $description->getElementsByTagName('title-info')->item(0);
                        //Получаем список жанров, к которым относится книга
                        $genre_list = $title_info->getElementsByTagName('genre');
                        if (count($genre_list) == 0) {$fb2error = 1;}
                        foreach ($genre_list as $element) {
                            //Помещаем список жанров в массив
                            $genres[] = $element->nodeValue;
                        }
                        //Получаем список авторов.
                        $authors_list = $title_info->getElementsByTagName('author');
                        $element = '';
                        if (count($authors_list) == 0) {$fb2error = 1;}

                        foreach ($authors_list as $element) {
                            $authors = array($element->getElementsByTagName('first-name')->item(0)->nodeValue,
                                $element->getElementsByTagName('last-name')->item(0)->nodeValue,
                                $element->getElementsByTagName('middle-name')->item(0)->nodeValue,
                                $element->getElementsByTagName('nickname')->item(0)->nodeValue,
                                $element->getElementsByTagName('email')->item(0)->nodeValue);
                        }

                        //Получаем оставшуюся информацию о книге:
                        //Название книги
                        $book_title = $title_info->getElementsByTagName('book-title')->item(0)->nodeValue;
                        //Аннотация
                        $annotation = trim($title_info->getElementsByTagName('annotation')->item(0)->nodeValue);
                        //Дата
                        $date = $title_info->getElementsByTagName('date')->item(0)->nodeValue;
                        //Серия
                        if (empty($title_info->getElementsByTagName('sequence')->item(0))) {
                            $sequence = '';
                            $sequence_number = 0;
                        } else {
                            $sequence = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('name');
                            $sequence_number = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('number');
                        }
                    }
                    $res["data"] = array(
                        "book_title" => $book_title, 
                        "hash_name" => $filename,
                    );
                }
            } else {
                $res["error"] = "PDO Error";
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
