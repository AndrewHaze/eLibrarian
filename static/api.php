<?php
//Включение буферизации вывода
//что-бы не портить JSON
ob_start();

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
        //Отметка о прочтении
        case "status_read":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = $_POST["state"];
                $stmt = $pdo->prepare('UPDATE `books`
                SET bk_read = :b_state
                WHERE bk_id = :b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = :login);');
                $stmt->bindValue(':b_state', $b_state, PDO::PARAM_INT);
                $stmt->bindValue(':b_id', $b_id, PDO::PARAM_INT);
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error ".$username.' - '.$b_state;
                }
            }
            break;
        //Отметка о предпочтении
        case "status_favorites":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = $_POST["state"];
                $stmt = $pdo->prepare('UPDATE `books`
                SET bk_favorites = :b_state
                WHERE bk_id = :b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = :login);');
                $stmt->bindValue(':b_state', $b_state, PDO::PARAM_INT);
                $stmt->bindValue(':b_id', $b_id, PDO::PARAM_INT);
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error ".$username.' - '.$b_state;
                }
            }
            break;
        //Отметка о планировании
        case "status_toplan":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = $_POST["state"];
                $stmt = $pdo->prepare('UPDATE `books`
                SET bk_to_plan = :b_state
                WHERE bk_id = :b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = :login);');
                $stmt->bindValue(':b_state', $b_state, PDO::PARAM_INT);
                $stmt->bindValue(':b_id', $b_id, PDO::PARAM_INT);
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error ".$username.' - '.$b_state;
                }
            }
            break;       
        //Отметка о планировании
        case "set_stars":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = $_POST["state"];
                $stmt = $pdo->prepare('UPDATE `books`
                SET bk_stars = :b_state
                WHERE bk_id = :b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = :login);');
                $stmt->bindValue(':b_state', $b_state, PDO::PARAM_INT);
                $stmt->bindValue(':b_id', $b_id, PDO::PARAM_INT);
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error ".$username.' - '.$b_state;
                }
            }
            break;          
        //набор букв для фильтра авторов
        case "с_list":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $stmt = $pdo->prepare('SELECT DISTINCT SUBSTR(ar_last_name, 1, 1) FROM authors, books_authors, books
                                           WHERE ar_id = bkar_ar_id AND bkar_bk_id = bk_id
                                             AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = :login) ORDER BY 1');
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($result as $value) {
                    array_push($res["data"], array("text" => $value, "value" => $value));
                }
                array_unshift($res["data"], array("text" => "*", "value" => "*"));
            }
            break;
        //список авторов
        case "a_list":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $stmt = $pdo->prepare('SELECT ar_id, ar_last_name, ar_middle_name, ar_first_name, COUNT(*) cnt
                                       FROM authors, books_authors, books, users
                                       WHERE ar_id = bkar_ar_id
                                         AND bkar_bk_id = bk_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = :login
                                      GROUP BY ar_id, ar_last_name, ar_middle_name, ar_first_name
                                      ORDER BY ar_last_name, ar_middle_name, ar_first_name');
                $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $value) {
                    array_push($res["data"],
                        array(
                            "id" => "ai" . $value[ar_id],
                            "books" => $value[cnt],
                            "author" => ucwords($value[ar_last_name] . ' ' . $value[ar_first_name] . ' ' . $value[ar_middle_name]),
                            "isActive" => false,
                        ));
                }

            }
            break;
        //конкретный автор
        case "author":
            if ($pdo and $_SESSION["user"]) {
                $stmt = $pdo->prepare('SELECT ar_last_name, ar_middle_name, ar_first_name
                                       FROM authors
                                       WHERE ar_id = :ai');
                $stmt->bindValue(':ai', $username, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $value) {
                    $res["data"] = ucwords($value[ar_last_name] . ' ' . $value[ar_first_name] . ' ' . $value[ar_middle_name]);
                }

            }
            break;    
        //список серий    
        case "as_list":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $ai = $_POST["dat"];
                $stmt = $pdo->prepare('SELECT DISTINCT se_id, se_title
                                        FROM books, books_authors, books_series, series, users
                                        WHERE bkar_ar_id = :ai
                                         AND bkar_bk_id = bk_id
                                         AND bkse_bk_id = bk_id
                                         AND se_id = bkse_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = :login1
                                         ORDER BY se_title');
                $stmt->bindValue(':login1', $username, PDO::PARAM_STR);
                $stmt->bindValue(':ai', $ai, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "se" . $value[se_id],
                                "seriesTitle" => $value[se_title],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        //список книг
        case "ab_list":
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $ai = $_POST["dat"];
                $stmt = $pdo->prepare('SELECT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars,
                                              bkse_number, 
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, " ", ar_middle_name, " ", ar_first_name
                                                      ORDER BY ar_last_name, ar_middle_name, ar_first_name ASC SEPARATOR ", ")
                                                FROM books_authors, authors, users
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = :login2
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ", ")
                                                FROM books_genres, genres, users
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = :login3
                                              ) list_genres
                                        FROM books_authors, books, books_series, series, users
                                       WHERE bkar_ar_id = :ai
                                         AND bkar_bk_id = bk_id
                                         AND bkse_bk_id = bk_id
                                         AND se_id = bkse_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = :login1
                                         ORDER BY se_title, bkse_number, bk_title');
                $stmt->bindValue(':login1', $username, PDO::PARAM_STR);
                $stmt->bindValue(':login2', $username, PDO::PARAM_STR);
                $stmt->bindValue(':login3', $username, PDO::PARAM_STR);
                $stmt->bindValue(':ai', $ai, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "bk" . $value[bk_id],
                                "author" => ucwords($value[list_authors]),
                                "title" => $value[bk_title],
                                "cover" => base64_encode($value[bk_cover]),
                                "genres" => $value[list_genres] ?: "Прочее",
                                "annotation" => $value[bk_annotation],
                                "seriesTitle" => $value[se_title],
                                "seriesNumber" => $value[bkse_number],
                                "isRead" => $value[bk_read],
                                "isToPlan" => $value[bk_to_plan],
                                "isFavorites" => $value[bk_favorites],
                                "howManyStars" => $value[bk_stars],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "clear_upload": //очистка папки uploads, для текщей сессии
            $res["data"] = clear_dir('uploads');
            if (!$res["data"]) {
                $res["success"] = false;
                $res["error"] = "I/O Error (Clear Upload)";
            }
            break;
        case "proc":
            if ($pdo and $_SESSION["user"]) {
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
                // Нужно проверить документ перед тем как ссылаться по идентификатору
                $doc->validateOnParse = true;
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

                        //Обложка
                        $coverpage = $title_info->getElementsByTagName('coverpage')->item(0);
                        $cover_id = substr($coverpage->getElementsByTagName('image')->item(0)->getAttribute('l:href'), 1);
                        $nodes = $doc->getElementsByTagName('binary');
                        foreach ($nodes as $node) {
                            if ($node->getAttribute('id') == $cover_id) {
                                $cover = base64_decode($node->nodeValue);
                                break;
                            }
                        }

                        //Дата
                        $book_date = $title_info->getElementsByTagName('date')->item(0)->nodeValue;

                        //Подгружаем секцию 'document-info'
                        $document_info = $description->getElementsByTagName('document-info')->item(0);

                        //book id
                        $book_id = $document_info->getElementsByTagName('id')->item(0)->nodeValue;
                        $time = strtotime($book_date);

                        /********************************** QUERIES ***********************************************/

                        $stmt = $pdo->prepare('INSERT INTO books (bk_ur_id, bk_book_id, bk_title, bk_annotation, bk_file_date, bk_file, bk_cover)
                                                   VALUES ((SELECT ur_id FROM users WHERE ur_login = :login), :book_id, :book_title, :book_annotation, :book_date, :book_file, :book_cover);');
                        $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_STR);
                        $stmt->bindValue(':book_title', $book_title, PDO::PARAM_STR);
                        $stmt->bindValue(':book_annotation', $book_annotation, PDO::PARAM_STR);
                        $stmt->bindValue(':book_date', date('Y-m-d', $time));
                        $stmt->bindValue(':book_file', $filename, PDO::PARAM_STR);
                        $stmt->bindValue(':book_cover', $cover, PDO::PARAM_LOB);

                        if ($stmt->execute()) {
                            $id_book = $pdo->lastInsertId();

                            //Получаем список жанров, к которым относится книга
                            $genre_list = $title_info->getElementsByTagName('genre');
                            if (count($genre_list) > 0) {
                                foreach ($genre_list as $element) {
                                    $genre = $element->nodeValue;
                                    $stmt = $pdo->prepare('INSERT INTO books_genres (bkge_bk_id, bkge_ge_id)
                                                                   VALUES (:id_books, (SELECT ge_id FROM genres WHERE ge_code = :genre_code));');
                                    $stmt->bindValue(':id_books', $id_book, PDO::PARAM_INT);
                                    $stmt->bindValue(':genre_code', $genre, PDO::PARAM_STR);
                                    $stmt->execute();
                                }
                            }

                            //Получаем список авторов.
                            $authors_list = $title_info->getElementsByTagName('author');
                            if (count($authors_list) > 0) {
                                $element = '';
                                foreach ($authors_list as $element) {
                                    $author = array($element->getElementsByTagName('first-name')->item(0)->nodeValue,
                                        $element->getElementsByTagName('last-name')->item(0)->nodeValue,
                                        $element->getElementsByTagName('middle-name')->item(0)->nodeValue);

                                    $first_name = $author[0] ?: "";
                                    $last_name = $author[1] ?: "";
                                    $middle_name = $author[2] ?: "";

                                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM authors
                                                               WHERE ar_first_name = :first_name AND ar_last_name = :last_name AND ar_middle_name = :middle_name ');
                                    $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
                                    $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
                                    $stmt->bindValue(':middle_name', $middle_name, PDO::PARAM_STR);
                                    $stmt->execute();
                                    $count = $stmt->fetchColumn();
                                    if ($count === 0) {
                                        $stmt = $pdo->prepare('INSERT INTO authors (ar_first_name, ar_last_name, ar_middle_name)
                                                                   VALUES (:first_name, :last_name, :middle_name);');
                                        $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
                                        $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
                                        $stmt->bindValue(':middle_name', $middle_name, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $id_author = $pdo->lastInsertId();
                                    } else {
                                        $stmt = $pdo->prepare('SELECT ar_id FROM authors
                                                                   WHERE ar_first_name = :first_name AND ar_last_name = :last_name AND ar_middle_name = :middle_name ');
                                        $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
                                        $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
                                        $stmt->bindValue(':middle_name', $middle_name, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $id_author = $stmt->fetchColumn();
                                    }
                                    //книги-авторы
                                    $stmt = $pdo->prepare('INSERT INTO books_authors (bkar_bk_id, bkar_ar_id)
                                                                   VALUES (:id_books, :id_authors);');
                                    $stmt->bindValue(':id_books', $id_book, PDO::PARAM_INT);
                                    $stmt->bindValue(':id_authors', $id_author, PDO::PARAM_INT);
                                    $stmt->execute();

                                }
                            }

                            $res["data"] = array(
                                //имя файла с книгой
                                "hash_name" => $filename,
                            );
 
                            //Серия
                            if (empty($title_info->getElementsByTagName('sequence')->item(0))) {
                                $sequence = "яяяяяя"; //Для сортировки
                                $sequence_number = 0;
                            } else {
                                $sequence = trim($title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('name'));
                                $sequence_number = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('number');
                            }
                            if (trim($sequence) == '') {
                                $sequence = "яяяяяя";
                            }
                            //Серия есть?
                            $stmt = $pdo->prepare('SELECT COUNT(*) FROM series
                                                    WHERE se_title = :se_title ');
                            $stmt->bindValue(':se_title', $sequence, PDO::PARAM_STR);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();
                            if ($count === 0) { //Новая серия
                                $stmt = $pdo->prepare('INSERT INTO series (se_title) VALUES (:se_title);');
                                $stmt->bindValue(':se_title', $sequence, PDO::PARAM_STR);
                                $stmt->execute();
                                $id_sequence = $pdo->lastInsertId();
                            } else { //Существующая серия
                                $stmt = $pdo->prepare('SELECT se_id FROM series
                                                        WHERE se_title = :se_title');
                                $stmt->bindValue(':se_title', $sequence, PDO::PARAM_STR);
                                $stmt->execute();
                                $id_sequence = $stmt->fetchColumn();
                            }

                            $stmt = $pdo->prepare('INSERT INTO books_series (bkse_bk_id, bkse_se_id, bkse_number)
                                                          VALUES (:id_books, :id_sequence, :se_number);');
                            $stmt->bindValue(':id_books', $id_book, PDO::PARAM_INT);
                            $stmt->bindValue(':id_sequence', $id_sequence, PDO::PARAM_INT);
                            $stmt->bindValue(':se_number', $sequence_number, PDO::PARAM_INT);
                            $stmt->execute();

                        } else {
                            //Ошибка обновления БД
                            $res["success"] = false;
                            $res["error"] = "dbe";
                        }

                    } else {
                        //Требуется описание книги
                        $res["success"] = false;
                        $res["error"] = "ndf";
                    }
                } else {
                    //Нет коннекта или авторизации
                    $res["success"] = false;
                    $res["error"] = "dbe";
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
