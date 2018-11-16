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
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
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
                $stmt = $pdo->prepare('INSERT INTO `users` ( `ur_login`, `ur_hash`) VALUES (:login, :hash);');
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
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
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
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
                    $username = $_SESSION["user"];
                    $filename = $_POST["file"];
                    $file = 'uploads/' . $filename;
                    $fileext = pathinfo($file, PATHINFO_EXTENSION);
                    //Создаем XML документ
                    $doc = new DOMDocument();
                    //Отключаем проверку ошибок
                    $doc->strictErrorChecking = false;
                    $doc->recover = true;
                    if ($fileext == "fb2") {
                        //Загружаем содержимое файла
                        $load = $doc->load($file, LIBXML_NOERROR);
                    } else if ($fileext == "zip") {
                        $zip = new ZipArchive();
                        if ($zip->open($file)) {
                            $res["error"] = "per";
                            $data = $zip->getFromIndex(0);
                            $load = $doc->loadXML($data);
                        } else {
                            $load = false;
                            $res["error"] = "dma";
                        }
                    }
                    if ($load) {
                        //Получаем содержимое секции <description>
                        $description = $doc->getElementsByTagName('description');
                        $description = $description->item(0);
                        if ($description) {
                            //Подгружаем секцию 'title-info'
                            $title_info = $description->getElementsByTagName('title-info')->item(0);

                            //Название книги
                            $book_title = $title_info->getElementsByTagName('book-title')->item(0)->nodeValue;
                            //Аннотация
                            $book_annotation = trim($title_info->getElementsByTagName('annotation')->item(0)->nodeValue);
                            //Дата
                            $book_date = $title_info->getElementsByTagName('date')->item(0)->nodeValue;

                            //Подгружаем секцию 'document-info'
                            $document_info = $description->getElementsByTagName('document-info')->item(0);
                            //book id
                            $book_id = $document_info->getElementsByTagName('id')->item(0)->nodeValue;

                            //books.db
                            $time = strtotime($book_date);
                            echo $time;
                            $stmt = $pdo->prepare('INSERT INTO `books` (`bk_owner`, `bk_book_id`, `bk_title`, `bk_annotation`, `bk_file_date`, `bk_file`)
                                                   VALUES ((SELECT ur_id FROM users WHERE ur_login = :login), :book_id, :book_title, :book_annotation, :book_date, :book_file);');
                            $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                            $stmt->bindValue(':book_id', $book_id, PDO::PARAM_STR);
                            $stmt->bindValue(':book_title', $book_title, PDO::PARAM_STR);
                            $stmt->bindValue(':book_annotation', $book_annotation, PDO::PARAM_STR);
                            $stmt->bindValue(':book_date', date('Y-m-d', $time));
                            $stmt->bindValue(':book_file', $filename, PDO::PARAM_STR);

                            if ($stmt->execute()) {
                                $id_book = $pdo->lastInsertId(); 
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
                                            $element->getElementsByTagName('middle-name')->item(0)->nodeValue);

                                        $authors[0] = $authors[0] ?: "";
                                        $authors[1] = $authors[1] ?: "";
                                        $authors[2] = $authors[2] ?: "";

                                        $stmt = $pdo->prepare('SELECT COUNT(*) FROM authors
                                                               WHERE ar_first_name = :first_name AND ar_last_name = :last_name AND ar_middle_name = :middle_name ');
                                        $stmt->bindValue(':first_name', $authors[0], PDO::PARAM_STR);
                                        $stmt->bindValue(':last_name', $authors[1], PDO::PARAM_STR);
                                        $stmt->bindValue(':middle_name', $authors[2], PDO::PARAM_STR);
                                        $stmt->execute();
                                        $count = $stmt->fetchColumn();
                                        if ($count === 0) {
                                            $stmt = $pdo->prepare('INSERT INTO `authors` (`ar_first_name`, `ar_last_name`, `ar_middle_name`)
                                                                   VALUES (:first_name, :last_name, :middle_name);');
                                            $stmt->bindValue(':first_name', $authors[0], PDO::PARAM_STR);
                                            $stmt->bindValue(':last_name', $authors[1], PDO::PARAM_STR);
                                            $stmt->bindValue(':middle_name', $authors[2], PDO::PARAM_STR);
                                            $stmt->execute();
                                            $id_author = $pdo->lastInsertId(); 
                                            //книги-авторы 
                                            $stmt = $pdo->prepare('INSERT INTO `books_authors` (`bkar_bk_id`, `bkar_ar_id`)
                                                                   VALUES (:id_books, :id_authors);');
                                            $stmt->bindValue(':id_books', $id_book, PDO::PARAM_INT);
                                            $stmt->bindValue(':id_authors', $id_author, PDO::PARAM_INT);
                                            $stmt->execute();
                                        }
                                    }
                                }

                                $res["data"] = array(
                                    //"test" => $count,
                                    "hash_name" => $filename,
                                );

                                //Серия
                                if (empty($title_info->getElementsByTagName('sequence')->item(0))) {
                                    $sequence = '';
                                    $sequence_number = 0;
                                } else {
                                    $sequence = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('name');
                                    $sequence_number = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('number');
                                }

                            } else {
                                $res["success"] = false;
                                $res["error"] = "dbe";
                            }

                        } else {
                            $res["success"] = false;
                            $res["error"] = "ndf";
                        }
                    } else {
                        $res["success"] = false;
                    }
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
ob_end_clean(); //Чистим буфер
echo json_encode($res);
