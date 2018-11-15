<?php
//Включение буферизации вывода
//что-бы не портить JSON
ob_start(); 

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
        "isActive" => FALSE,
        "_rowVariant" => "",
    ),
    array(
        "id" => 2,
        "books" => 15,
        "author" => "Бушков",
        "isActive" => FALSE,
        "_rowVariant" => "",
    ),
    array(
        "id" => 3,
        "books" => 3,
        "author" => "Пехов",
        "isActive" => FALSE,
        "_rowVariant" => "",
    ),
    array(
        "id" => 4,
        "books" => 12,
        "author" => "Панов",
        "isActive" => FALSE,
        "_rowVariant" => "",
    ),
);

require_once 'functions.php';

set_cors();

$res = array("data" => array(), "success" => TRUE, "error" => "");

$_POST = json_decode(file_get_contents("php://input"), TRUE);

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
                    $res["data"] = FALSE;
                } else {
                    $res["data"] = TRUE;
                }
            } else {
                $res["success"] = FALSE;
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
                    $res["data"] = FALSE;
                } else {
                    $res["data"] = TRUE;
                }
            } else {
                $res["success"] = FALSE;
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
                $_SESSION['user'] = NULL;
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
                    $_SESSION['user'] = NULL;
                    $res["data"] = FALSE;
                    $res["error"] = "Wrong Password";
                }
            } else {
                $_SESSION['user'] = NULL;
                $res["success"] = FALSE;
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
                $res["success"] = FALSE;
                $res["error"] = "I/O Error (Clear Upload)";
            }
            break;
        case "proc":
            if ($pdo) {
                if ($_SESSION["user"]) {
                    $filename = 'uploads/' . $_POST["file"];
                    $fileext = pathinfo($filename, PATHINFO_EXTENSION);
                    //Создаем XML документ
                    $doc = new DOMDocument();
                    //Отключаем проверку ошибок
                    $doc->strictErrorChecking = FALSE;
                    $doc->recover = TRUE;
                    if ($fileext == "fb2") {
                        //Загружаем содержимое файла
                        $load = $doc->load($filename, LIBXML_NOERROR);
                    } else if ($fileext == "zip") {
                        $zip = new ZipArchive();
                        if ($zip->open($filename)) {
                            $res["error"] = "per";
                            $data = $zip->getFromIndex(0);
                            $load = $doc->loadXML($data);
                        } else {
                            $load = FALSE;
                            $res["error"] = "dma";
                        }
                    }
                    if ($load) {
                        //Получаем содержимое секции <description>
                        $description = $doc->getElementsByTagName('description');
                        $description = $description->item(0);
                        if ($description) {
                            //Получаем название книги
                            $title_info = $description->getElementsByTagName('title-info')->item(0);

                            //Получаем список жанров, к которым относится книга
                            $genre_list = $title_info->getElementsByTagName('genre');
                            if (count($genre_list) > 0) {
                                foreach ($genre_list as $element) {
                                    //Помещаем список жанров в массив
                                    $genres[] = $element->nodeValue;
                                }
                            }
                            //Получаем список авторов.
                            $authors_list = $title_info->getElementsByTagName('author');

                            if (count($authors_list) > 0) {
                                $element = '';
                                foreach ($authors_list as $element) {
                                    $authors = array($element->getElementsByTagName('first-name')->item(0)->nodeValue,
                                        $element->getElementsByTagName('last-name')->item(0)->nodeValue,
                                        $element->getElementsByTagName('middle-name')->item(0)->nodeValue,
                                        $element->getElementsByTagName('nickname')->item(0)->nodeValue,
                                        $element->getElementsByTagName('email')->item(0)->nodeValue);
                                }
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

                            $res["data"] = array(
                                "book_title" => $book_title,
                                "hash_name" => $filename,
                            );
                        } else {
                            $res["success"] = FALSE;
                            $res["error"] = "ndf";
                        }
                    } else {
                        $res["success"] = FALSE;
                    }
                }
            } else {
                $res["error"] = "PDO Error";
            }
            break;
        default:
            $res["success"] = FALSE;
            $res["error"] = "Unknown command";
    }
} else {
    $res["success"] = FALSE;
    $res["error"] = "No command";
}
ob_end_clean(); //Чистим буфер
echo json_encode($res);
