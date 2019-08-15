<?php
//Включение буферизации вывода
//что-бы не портить JSON
ob_start();

require_once 'functions.php';

set_cors();

$res = array("data" => array(), "success" => true, "error" => "");

$_POST = json_decode(file_get_contents("php://input"), true);

if (isset($_POST["cmd"])) {
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
                $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE ur_login = '$username'");
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
                $stmt = $pdo->exec("INSERT INTO `users` ( `ur_login`, `ur_hash`) VALUES ('$username', '$hash');");
                //добавить проверку
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
                $stmt = $pdo->query("SELECT ur_hash FROM users WHERE ur_login = '$username'");
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
        case "check": //логин
            $res["data"] = $_SESSION['user'];
            break;
        case "status_read": //Отметка о прочтении
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = intval($_POST["state"]);
                $stmt = $pdo->exec("UPDATE `books` SET bk_read = $b_state WHERE bk_id = $b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username');");
                if ($stmt > 0) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error " . $b_id . ' - ' . $b_state;
                }
            }
            break;
        case "status_favorites": //Отметка о предпочтении
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = intval($_POST["state"]);
                $stmt = $pdo->exec("UPDATE `books`
                SET bk_favorites = $b_state
                WHERE bk_id = $b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username');");
                if ($stmt > 0) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error " . $username . ' - ' . $b_state;
                }
            }
            break;
        case "status_toplan": //Отметка о планировании
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = intval($_POST["state"]);
                $stmt = $pdo->exec("UPDATE `books`
                SET bk_to_plan = $b_state
                WHERE bk_id = $b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username');");
                if ($stmt > 0) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error " . $username . ' - ' . $b_state;
                }
            }
            break;
        case "set_stars": //Рейтинг
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $b_id = $_POST["id"];
                $b_state = $_POST["state"];
                $stmt = $pdo->exec("UPDATE `books`
                SET bk_stars = $b_state
                WHERE bk_id = $b_id AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username');");
                if ($stmt > 0) {
                    $res["data"] = true;
                } else {
                    $res["success"] = false;
                    $res["error"] = "DB Update Error " . $username . ' - ' . $b_state;
                }
            }
            break;
        case "с_list": //набор букв для фильтра
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $dat = $_POST["dat"];
                switch ($dat) {
                    case "authors":
                        $stmt = $pdo->query("SELECT DISTINCT SUBSTR(ar_last_name, 1, 1) FROM authors, books_authors, books
                                           WHERE ar_id = bkar_ar_id AND bkar_bk_id = bk_id
                                             AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username') ORDER BY 1");
                        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($result as $value) {
                            array_push($res["data"], array("text" => $value, "value" => $value));
                        }
                        break;
                    case "series":
                        $stmt = $pdo->query("SELECT DISTINCT SUBSTR(se_title, 1, 1) FROM books, series
                                           WHERE se_id = bk_se_id
                                             AND se_title != 'яяяяяя'
                                             AND bk_ur_id = (SELECT ur_id FROM users WHERE ur_login = '$username') ORDER BY 1");
                        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($result as $value) {
                            array_push($res["data"], array("text" => $value, "value" => $value));
                        }
                        break;
                    case "genres":
                        $stmt = $pdo->query("SELECT SUBSTR(gg_title, 1, 1) FROM genres_groups
                                               UNION
                                               SELECT SUBSTR(ge_title, 1, 1) FROM genres
                                               ORDER BY 1");
                        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($result as $value) {
                            array_push($res["data"], array("text" => $value, "value" => $value));
                        }
                        break;
                }
                array_unshift($res["data"], array("text" => "*", "value" => "*"));
            }
            break;
        case "a_list": //список авторов
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $stmt = $pdo->query("SELECT ar_id, ar_last_name, ar_middle_name, ar_first_name, COUNT(*) cnt
                                       FROM authors, books_authors, books, users
                                       WHERE ar_id = bkar_ar_id
                                         AND bkar_bk_id = bk_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                      GROUP BY ar_id, ar_last_name, ar_middle_name, ar_first_name
                                      ORDER BY ar_last_name, ar_middle_name, ar_first_name");
                $result = $stmt->fetchAll();
                foreach ($result as $value) {
                    array_push($res["data"],
                        array(
                            "id" => "ai" . $value['ar_id'],
                            "books" => $value['cnt'],
                            //Склеиваем и удаляем лишнии пробелы + все заглавные
                            "author" => preg_replace('/^ +| +$|( ) +/m', '$1', ucwords($value['ar_last_name'] . ' ' . $value['ar_first_name'] . ' ' . $value['ar_middle_name'])),
                            "isActive" => false,
                        ));
                }

            }
            break;
        case "author": //выбраный автор
            if ($pdo and $_SESSION["user"]) {
                $ai = $_POST["dat"];
                $stmt = $pdo->query("SELECT ar_last_name, ar_middle_name, ar_first_name
                                       FROM authors
                                       WHERE ar_id = $ai");
                $result = $stmt->fetch();
                $res["data"] = ucwords($result['ar_last_name'] . ' ' . $result['ar_first_name'] . ' ' . $result['ar_middle_name']);

            }
            break;
        case "series": //серия выбранной книги
            if ($pdo and $_SESSION["user"]) {
                $b_id = $_POST["dat"];
                $stmt = $pdo->query("SELECT se_title, bk_number
                                        FROM books, series
                                        WHERE bk_id = $b_id
                                          AND se_id = bk_se_id");
                if ($stmt->execute()) {
                    $result = $stmt->fetch();
                    if ($result[se_title] == 'яяяяяя') {
                        $res["data"] = '';
                    } else {
                        $res["data"] = ucwords($result['se_title']) . ' №' . $result['bk_number'];
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "as_list": //список серий автора
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $ai = $_POST["dat"];
                $stmt = $pdo->query("SELECT DISTINCT se_id, se_title
                                        FROM books, books_authors, series, users
                                        WHERE bkar_ar_id = $ai
                                         AND bkar_bk_id = bk_id
                                         AND se_id = bk_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "se" . $value['se_id'],
                                "seriesTitle" => $value['se_title'],
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "series_by_condition": //серии по условию
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                unset($condition);
                switch ($_POST["dat"]) {
                    case 1:
                        $condition = ' ';
                        break;
                    case 2:
                        $condition = " AND bk_read = 1 ";
                        break;
                    case 3:
                        $condition = " AND bk_to_plan = 1 ";
                        break;
                    case 4:
                        $condition = " AND bk_favorites = 1 ";
                        break;
                    case 5:
                        $condition = " AND YEARWEEK(CURDATE()) = YEARWEEK(bk_added) ";
                        break;
                    case 6:
                        $condition = " AND YEAR(CURDATE()) = YEAR(bk_added) AND MONTH(CURDATE()) = MONTH(bk_added) ";
                        break;
                    case 7:
                        $condition = " AND YEAR(CURDATE()) = YEAR(bk_added) ";
                        break;
                    case 8:
                        $condition = " AND bk_added >= '" . $_POST['dat1'] . "' AND bk_added <= '" . $_POST['dat2'] . "' ";
                        //$condition = " ";
                        break;
                    default:
                        $condition = " ";
                }
                $stmt = $pdo->query("SELECT DISTINCT se_id, se_title
                                        FROM books, series, users
                                       WHERE se_id = bk_se_id"
                    . $condition .
                    "AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "se" . $value['se_id'],
                                "seriesTitle" => $value['se_title'],
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "books_by_condition": //книги по условию
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                unset($condition);
                switch ($_POST["dat"]) {
                    case 1:
                        $condition = " ";
                        break;
                    case 2:
                        $condition = " AND bk_read = 1 ";
                        break;
                    case 3:
                        $condition = " AND bk_to_plan = 1 ";
                        break;
                    case 4:
                        $condition = " AND bk_favorites = 1 ";
                        break;
                    case 5:
                        $condition = " AND YEAR(CURDATE()) = YEAR(bk_added) AND MONTH(CURDATE()) = MONTH(bk_added) AND WEEK(CURDATE()) = WEEK(bk_added) ";
                        break;
                    case 6:
                        $condition = " AND YEAR(CURDATE()) = YEAR(bk_added) AND MONTH(CURDATE()) = MONTH(bk_added) ";
                        break;
                    case 7:
                        $condition = " AND YEAR(CURDATE()) = YEAR(bk_added) ";
                        break;
                    case 8:
                        $condition = " AND bk_added >= '" . $_POST['dat1'] . "' AND bk_added <= '" . $_POST['dat2'] . "' ";
                        break;
                    default:
                        $condition = " ";
                }
                $stmt = $pdo->query("SELECT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars, bk_file,
                                              bk_number,
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, ' ', ar_first_name, ' ', ar_middle_name
                                                      ORDER BY ar_last_name, ar_first_name, ar_middle_name ASC SEPARATOR ', ')
                                                FROM books_authors, authors, users
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ', ')
                                                FROM books_genres, genres, users
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_genres
                                        FROM books, series, users
                                       WHERE se_id = bk_se_id
                                         AND bk_ur_id = ur_id"
                    . $condition .
                    "AND ur_login = '$username'
                                         ORDER BY se_title, bk_number, bk_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "bk" . $value['bk_id'],
                                "author" => ucwords($value['list_authors']),
                                "title" => $value['bk_title'],
                                "cover" => base64_encode($value['bk_cover']),
                                "genres" => $value['list_genres'] ?: "Прочее",
                                "annotation" => $value['bk_annotation'],
                                "seriesTitle" => $value['se_title'],
                                "seriesNumber" => $value['bk_number'],
                                "isRead" => $value['bk_read'],
                                "isToPlan" => $value['bk_to_plan'],
                                "isFavorites" => $value['bk_favorites'],
                                "howManyStars" => $value['bk_stars'],
                                "fileName" => $value['bk_file'],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "book": //книга
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->query("SELECT *
                                       FROM books
                                       WHERE bk_id = $bi");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "bk_title" => $value['bk_title'],
                                "bk_src_title" => $value['bk_src_title'],
                                "bk_cover" => base64_encode($value['bk_cover']),
                                "bk_annotation" => $value['bk_annotation'],
                                "bk_seriesNumber" => $value['bk_number'],
                                "bk_file_name" => $value['bk_file'],
                                "bk_date" => $value['bk_date'],
                                "bk_keywords" => $value['bk_keywords'],
                                "bk_translators" => $value['bk_translators'],
                                "bk_doc_id" => $value['bk_doc_id'],
                                "bk_doc_authors" => $value['bk_doc_authors'],
                                "bk_doc_programms" => $value['bk_doc_programms'],
                                "bk_doc_date" => $value['bk_doc_date'],
                                "bk_doc_ocr_authors" => $value['bk_doc_ocr_authors'],
                                "bk_doc_ver" => $value['bk_doc_ver'],
                                "bk_doc_url" => $value['bk_doc_url'],
                                "bk_doc_history" => $value['bk_doc_history'],
                                "bk_pub_title" => $value['bk_pub_title'],
                                "bk_pub_publisher" => $value['bk_pub_publisher'],
                                "bk_pub_city" => $value['bk_pub_city'],
                                "bk_pub_year" => $value['bk_pub_year'],
                                "bk_pub_isbn" => $value['bk_pub_isbn'],
                                "bk_doc_file_name" => $value['bk_doc_file_name'],
                                "bk_doc_file_size" => $value['bk_doc_file_size'],
                                "bk_doc_file_date" => $value['bk_doc_file_date'],
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "b_authors": //авторы книги
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->query("SELECT ar_id, ar_last_name, ar_middle_name, ar_first_name
                FROM authors, books_authors, books
                WHERE bk_id = $bi
                  AND bkar_bk_id = bk_id
                  AND ar_id = bkar_ar_id
             ORDER BY ar_last_name, ar_middle_name, ar_first_name");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "ai" . $value['ar_id'],
                                //Склеиваем и удаляем лишнии пробелы + все заглавные
                                "author" => preg_replace('/^ +| +$|( ) +/m', '$1', ucwords($value['ar_last_name'] . ' ' . $value['ar_first_name'] . ' ' . $value['ar_middle_name'])),
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "ab_list": //список книг автора
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $ai = $_POST["dat"];
                $stmt = $pdo->query("SELECT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars, bk_file,
                                              bk_number,
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, ' ', ar_first_name, ' ', ar_middle_name
                                                      ORDER BY ar_last_name, ar_first_name, ar_middle_name ASC SEPARATOR ', ')
                                                FROM books_authors, authors
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ', ')
                                                FROM books_genres, genres
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                              ) list_genres
                                        FROM books_authors, books, series, users
                                       WHERE bkar_ar_id = $ai
                                         AND bkar_bk_id = bk_id
                                         AND se_id = bk_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title, bk_number, bk_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "bk" . $value['bk_id'],
                                "author" => ucwords($value['list_authors']),
                                "title" => $value['bk_title'],
                                "cover" => base64_encode($value['bk_cover']),
                                "genres" => $value['list_genres'] ?: "Прочее",
                                "annotation" => $value['bk_annotation'],
                                "seriesTitle" => $value['se_title'],
                                "seriesNumber" => $value['bk_number'],
                                "isRead" => $value['bk_read'],
                                "isToPlan" => $value['bk_to_plan'],
                                "isFavorites" => $value['bk_favorites'],
                                "howManyStars" => $value['bk_stars'],
                                "fileName" => $value['bk_file'],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "sa_list": //список всех серий
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $flag = $_POST["dat"]; //объем данных
                $stmt = $pdo->query("SELECT se_id, se_title, COUNT(*) cnt
                FROM books,
                     series,
                     users
                WHERE ur_login = '$username'
                  AND bk_ur_id = ur_id
                  AND se_id = bk_se_id
                  AND se_title != 'яяяяяя'
                 GROUP BY se_id, se_title
                ORDER BY se_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        if ($flag == 'simple') {
                            array_push($res["data"],
                                array(
                                    "id" => "se" . $value['se_id'],
                                    "seriesTitle" => $value['se_title'],
                                ));
                        } else {
                            array_push($res["data"],
                                array(
                                    "id" => "se" . $value['se_id'],
                                    "seriesTitle" => $value['se_title'],
                                    "books" => $value['cnt'],
                                    "isActive" => false,
                                ));
                        }
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "sb_list": //список книг серии
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $si = $_POST["dat"];
                $stmt = $pdo->query("SELECT DISTINCT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars, bk_file,
                                              bk_number,
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, ' ', ar_first_name, ' ', ar_middle_name
                                                      ORDER BY ar_last_name, ar_first_name, ar_middle_name ASC SEPARATOR ', ')
                                                FROM books_authors, authors, users
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ', ')
                                                FROM books_genres, genres, users
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_genres
                                        FROM books_authors, books, series, users
                                       WHERE bk_se_id = $si
                                         AND bkar_bk_id = bk_id
                                         AND se_id = bk_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title, bk_number, bk_title");
                if ($stmt) {
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
                                "seriesNumber" => $value[bk_number],
                                "isRead" => $value[bk_read],
                                "isToPlan" => $value[bk_to_plan],
                                "isFavorites" => $value[bk_favorites],
                                "howManyStars" => $value[bk_stars],
                                "fileName" => $value[bk_file],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "ser": //выбраная серия
            if ($pdo and $_SESSION["user"]) {
                $si = $_POST["dat"];
                $stmt = $pdo->query("SELECT se_id, se_title
                                       FROM series
                                       WHERE se_id = $si");
                $result = $stmt->fetchAll();
                foreach ($result as $value) {
                    array_push($res["data"],
                        array(
                            "id" => "se" . $value['se_id'],
                            "seriesTitle" => $value['se_title'],
                            "isActive" => false,
                        ));
                }

            }
            break;
        case "b_ser": //серия книги для BookEditor
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->query("SELECT se_id, se_title
                                       FROM books, series
                                       WHERE bk_id = $bi
                                       AND se_id = bk_se_id
                                       AND se_title != 'яяяяяя'");
                $result = $stmt->fetchAll();
                foreach ($result as $value) {
                    array_push($res["data"],
                        array(
                            "id" => "se" . $value['se_id'],
                            "seriesTitle" => $value['se_title'],
                        ));
                }

            }
            break;
        case "g_list": //список жанров
            if ($pdo and $_SESSION["user"]) {
                switch ($_POST["order"]) {
                    case 0:
                        $order_str = " ORDER BY 2, 4 ";
                        break;
                    case 1:
                        $order_str = " ORDER BY 2 DESC, 4";
                        break;
                    case 2:
                        $order_str = " ORDER BY 5 ASC, 2";
                        break;
                    case 3:
                        $order_str = " ORDER BY 5 DESC, 2";
                        break;
                }
                $username = $_SESSION["user"];

                $filter = $_POST["filter"];

                if ($filter === "*") {
                    $filter = null;
                }

                $filter = $filter."%";

                if ($_POST["type"] === "branch") {
                    $branch_id = $_POST["id"];
                } else {
                    $leaf_id = $_POST["id"];
                }

                $stmt = $pdo->query("SELECT gg_id, gg_title, ge_id, ge_title,
                                            (SELECT COUNT(1)
                                            FROM books_genres, genres g2, books, users
                                            WHERE g2.ge_gg_id = gg_id
                                            AND bkge_ge_id = g2.ge_id
                                            AND ge_id = bkge_ge_id
                                            AND bk_id = bkge_bk_id
                                            AND ur_id = bk_ur_id
                                            AND ur_login = '$username'
                                            AND (gg_title LIKE '$filter' OR g2.ge_title LIKE '$filter')) cnt1,
                                            (SELECT COUNT(1)
                                            FROM books_genres, books, users
                                            WHERE bkge_ge_id = ge_id
                                            AND bk_id = bkge_bk_id
                                            AND ur_id = bk_ur_id
                                            AND ur_login = '$username') cnt2
                                       FROM genres_groups, genres g1
                                       WHERE ge_gg_id = gg_id
                                       AND (gg_title LIKE '$filter' OR ge_title LIKE '$filter')" . $order_str);

                $result = $stmt->fetchAll();
                $g_group = '';
                $gg_count = 0;
                $gm_array = [];
                foreach ($result as $value) {
                    if ($value['gg_title'] != $g_group) {
                        if ($g_group != '') {
                            array_push($gm_array,
                                array(
                                    "id" => $g_id,
                                    "text" => $g_group,
                                    "type" => 'branch',
                                    "selected" => $b_selected,
                                    "opened" => $b_sopened,
                                    "children" => $gc_array,
                                    "count" => $gg_count,
                                    "disabled" => ($gg_count > 0) ? false : true,
                                ));

                            $l_selected = null;
                            if ($b_sopened) {
                                $b_sopened = false;
                            }
                        }
                        $g_group = $value['gg_title'];
                        $g_id = $value['gg_id'];
                        $b_selected = ($g_id === $branch_id) ? true : false;
                        $gg_count = $value['cnt1'];
                        $gc_array = [];
                    }
                    $l_selected = ($value['ge_id'] === $leaf_id) ? true : false;

                    if ($l_selected) {
                        $b_sopened = true;
                    }
                    array_push($gc_array,
                        array(
                            "id" => $value['ge_id'],
                            "text" => str_replace(" (то, что не вошло в другие категории)", "", $value['ge_title']),
                            "type" => 'leaf',
                            "selected" => $l_selected,
                            "count" => $value['cnt2'],
                            "disabled" => ($value['cnt2'] > 0) ? false : true,
                        ));
                }
                //Последняя группа вне цикла
                array_push($gm_array,
                    array(
                        "id" => $g_id,
                        "text" => $g_group,
                        "type" => 'branch',
                        "selected" => $b_selected,
                        "opened" => $b_sopened,
                        "children" => $gc_array,
                        "count" => $gg_count,
                        "disabled" => ($gg_count > 0) ? false : true,
                    ));
                $res["data"] = $gm_array;
            }
            break;
        ////////////////////////////////////////////////////////////////////
        case "bg_list": //список жанров для BookEditor
            if ($pdo and $_SESSION["user"]) {
                $stmt = $pdo->query("SELECT DISTINCT gg_title, ge_id, ge_code, ge_title
                                         FROM  genres, genres_groups
                                        WHERE gg_id = ge_gg_id
                                     ORDER BY 1, 4");
                $result = $stmt->fetchAll();
                $g_group = '';
                $gm_array = [];
                foreach ($result as $value) {
                    if ($value['gg_title'] != $g_group) {
                        if ($g_group != '') {
                            array_push($gm_array,
                                array(
                                    "group" => $g_group,
                                    "genres" => $gc_array,
                                ));
                        }
                        $g_group = $value['gg_title'];
                        $gc_array = [];
                    }
                    array_push($gc_array,
                        array(
                            "id" => $value['ge_id'],
                            "code" => $value['ge_code'],
                            "title" => str_replace(" (то, что не вошло в другие категории)", "", $value['ge_title']),
                        ));
                }
                //Последняя группа вне цикла
                array_push($gm_array,
                    array(
                        "group" => $g_group,
                        "genres" => $gc_array,
                    ));
                $res["data"] = $gm_array;
            }
            break;
        case "b_genres": //список серий в жанре
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->query("SELECT ge_id, ge_code, ge_title
                                         FROM books_genres, genres
                                        WHERE bkge_bk_id = $bi
                                          AND ge_id = bkge_ge_id
                                     ORDER BY ge_title");
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => $value['ge_id'],
                                "code" => $value['ge_code'],
                                "title" => str_replace(" (то, что не вошло в другие категории)", "", $value['ge_title']),
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "gs_list": //список серий в жанре
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $gi = $_POST["id"];
                if ($_POST["type"] === 'branch') {
                    $stmt = $pdo->query("SELECT DISTINCT se_id, se_title
                    FROM books,
                         genres_groups,
                         genres,
                         books_genres,
                         series,
                         users
                    WHERE gg_id = $gi
                      AND ge_gg_id = gg_id
                      AND bkge_ge_id = ge_id
                      AND bk_id = bkge_bk_id
                      AND se_id = bk_se_id
                      AND ur_login = '$username'
                      AND bk_ur_id = ur_id
                    ORDER BY se_title");
                } else {
                    $stmt = $pdo->prepare("SELECT DISTINCT se_id, se_title
                FROM books,
                     books_genres,
                     series,
                     users
                WHERE bkge_ge_id = $gi
                  AND bk_id = bkge_bk_id
                  AND se_id = bk_se_id
                  AND ur_login = '$username'
                  AND bk_ur_id = ur_id
                ORDER BY se_title");
                }
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "se" . $value['se_id'],
                                "seriesTitle" => $value['se_title'],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "gb_list": //список книг в жанре
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $gi = $_POST["id"];
                $filter = $_POST["filter"];
                if ($filter === "*") {
                    $filter = null;
                }
                if ($_POST["type"] === 'branch') {
                    $stmt = $pdo->query("SELECT DISTINCT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars, bk_file,
                                              bk_number,
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, ' ', ar_first_name, ' ', ar_middle_name
                                                      ORDER BY ar_last_name, ar_first_name, ar_middle_name ASC SEPARATOR ', ')
                                                FROM books_authors, authors, users
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ', ')
                                                FROM books_genres, genres, users
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_genres
                                        FROM  books, books_genres, genres_groups, genres, series, users
                                        WHERE gg_id = $gi
                                         AND ge_gg_id = gg_id
                                         AND bkge_ge_id = ge_id
                                         AND bk_id = bkge_bk_id
                                         AND se_id = bk_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title, bk_number, bk_title");

                } else {
                    $stmt = $pdo->query("SELECT DISTINCT bk_id, bk_title, bk_cover, bk_annotation, bk_read, bk_to_plan, bk_favorites, bk_stars, bk_file,
                                              bk_number,
                                              se_title,
                                              (SELECT GROUP_CONCAT(ar_last_name, ' ', ar_first_name, ' ', ar_middle_name
                                                      ORDER BY ar_last_name, ar_first_name, ar_middle_name ASC SEPARATOR ', ')
                                                FROM books_authors, authors, users
                                              WHERE bkar_bk_id = bk_id
                                                AND ar_id = bkar_ar_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_authors,
                                              (SELECT GROUP_CONCAT(ge_title ORDER BY ge_title ASC SEPARATOR ', ')
                                                FROM books_genres, genres, users
                                              WHERE bkge_bk_id = bk_id
                                                AND ge_id = bkge_ge_id
                                                AND bk_ur_id = ur_id
                                                AND ur_login = '$username'
                                              ) list_genres
                                        FROM  books, books_genres, series, users
                                        WHERE bkge_ge_id = $gi
                                         AND bk_id = bkge_bk_id
                                         AND se_id = bk_se_id
                                         AND bk_ur_id = ur_id
                                         AND ur_login = '$username'
                                         ORDER BY se_title, bk_number, bk_title");
                }
                if ($stmt) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => "bk" . $value['bk_id'],
                                "author" => ucwords($value['list_authors']),
                                "title" => $value['bk_title'],
                                "cover" => base64_encode($value['bk_cover']),
                                "genres" => $value['list_genres'] ?: "Прочее",
                                "annotation" => $value['bk_annotation'],
                                "seriesTitle" => $value['se_title'],
                                "seriesNumber" => $value['bk_number'],
                                "isRead" => $value['bk_read'],
                                "isToPlan" => $value['bk_to_plan'],
                                "isFavorites" => $value['bk_favorites'],
                                "howManyStars" => $value['bk_stars'],
                                "fileName" => $value['bk_file'],
                                "isActive" => false,
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "lg_list": //все языки
            if ($pdo and $_SESSION["user"]) {
                $stmt = $pdo->prepare('SELECT DISTINCT lg_id, lg_name
                FROM languages
                ORDER BY lg_name');
                if ($stmt->execute()) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "lg_id" => "lg" . $value[lg_id],
                                "lg_name" => $value[lg_name],
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "b_lang": //язык книги
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->prepare('SELECT DISTINCT lg_id, lg_name
                FROM books, languages
                WHERE bk_id = :bi
                AND lg_id = bk_lang');
                $stmt->bindValue(':bi', $bi, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => $value[lg_id],
                                "lg_name" => $value[lg_name],
                            ));
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "b_lang_src": //язык оригинала книги
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $stmt = $pdo->prepare('SELECT DISTINCT lg_id, lg_name
                FROM books, languages
                WHERE bk_id = :bi
                AND lg_id = bk_src_lang ');
                $stmt->bindValue(':bi', $bi, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $result = $stmt->fetchAll();
                    foreach ($result as $value) {
                        array_push($res["data"],
                            array(
                                "id" => $value[lg_id],
                                "lg_name" => $value[lg_name],
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
        case "proc": //Загрузка книг в БД
            if ($pdo and $_SESSION["user"]) {
                $username = $_SESSION["user"];
                $filename = $_POST["file"];
                $filesize = filesize("uploads/" . $filename);
                $filedate = $_POST["date"];
                $src_filename = $_POST["name"];

                $handle = fopen("uploads/bookparsing.log", "w");

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
                        $res["error"] = "per"; //Ошибка разбора
                        $data = $zip->getFromIndex(0);
                        $load = $doc->loadXML($data);
                    } else {
                        $load = false;
                        //fwrite($handle, "Поврежденный архив\n\n");
                        $res["error"] = "dma"; //Поврежденный архив
                    }
                }
                // Нужно проверить документ перед тем как ссылаться по идентификатору
                $doc->validateOnParse = true;
                if ($load) {
                    //fwrite($handle, "Файл открыт\n\n");
                    //Получаем содержимое секции <description>
                    $description = $doc->getElementsByTagName('description');
                    $description = $description->item(0);
                    if ($description) {
                        //Подгружаем секцию 'title-info'
                        $title_info = $description->getElementsByTagName('title-info')->item(0);
                        //Подгружаем секцию 'src-title-info'
                        $src_title_info = $description->getElementsByTagName('src-title-info')->item(0);

                        //Название книги
                        $book_title = $title_info->getElementsByTagName('book-title')->item(0)->nodeValue;
                        if ($src_title_info) {
                            $src_book_title = $src_title_info->getElementsByTagName('book-title')->item(0)->nodeValue;
                            if (!$src_title_info) {
                                $src_book_title = "";
                            }
                            //проверить как работает
                        } else {
                            $src_book_title = "";
                        }
                        //fwrite($handle, "Название книги: ".$book_title."\n\n");

                        //Аннотация
                        $book_annotation = preg_replace('/\s\s+/', chr(13), trim($title_info->getElementsByTagName('annotation')->item(0)->nodeValue));
                        //fwrite($handle, "Аннотация: ".$book_annotation."\n\n");

                        //Обложка
                        //fwrite($handle, "Обложка: " . $coverpage . "\n");
                        if (empty($title_info->getElementsByTagName('coverpage')->item(0))) {
                            $coverpage = false;
                            if ($src_title_info) {
                                if (!empty($src_title_info->getElementsByTagName('coverpage')->item(0))) {
                                    $coverpage = $src_title_info->getElementsByTagName('coverpage')->item(0);
                                }
                            }
                        } else {
                            $coverpage = $title_info->getElementsByTagName('coverpage')->item(0);
                        }

                        if ($coverpage) {
                            $cover_id = substr($coverpage->getElementsByTagName('image')->item(0)->getAttribute('l:href'), 1);
                            $nodes = $doc->getElementsByTagName('binary');
                            foreach ($nodes as $node) {
                                if ($node->getAttribute('id') == $cover_id) {
                                    $cover = base64_decode($node->nodeValue);
                                    //Масштабируем изображение
                                    $im = imagecreatefromstring($cover);
                                    $source_width = imagesx($im);
                                    $source_height = imagesy($im);
                                    $ratio = $source_height / $source_width;
                                    $new_width = 300; // новая ширина
                                    $new_height = $ratio * 300;
                                    $thumb = imagecreatetruecolor($new_width, $new_height);
                                    $transparency = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                                    imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparency);
                                    imagecopyresampled($thumb, $im, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
                                    imagedestroy($im);
                                    imagepng($thumb);
                                    $cover = ob_get_contents();
                                    break;
                                }
                            }

                        } else {
                            $cover = '';
                        }

                        $bk_keywords = $title_info->getElementsByTagName('keywords')->item(0)->nodeValue;

                        //Дата
                        if (empty($title_info->getElementsByTagName('date')->item(0))) {
                            $book_date = '2099-01-01';
                        } else {
                            $book_date = $title_info->getElementsByTagName('date')->item(0)->GetAttribute('value');
                            if (!$book_date || $book_date == '') {
                                $book_date = '2099-01-01';
                            }
                        }
                        $time = strtotime($book_date);
                        //fwrite($handle, "Дата написания".$book_date."\n\n");

                        //Язык
                        $book_language = $title_info->getElementsByTagName('lang')->item(0)->nodeValue;

                        //Язык оригинала
                        if (empty($title_info->getElementsByTagName('src-lang')->item(0))) {
                            $book_orig_language = $book_language;
                        } else {
                            $book_orig_language = $title_info->getElementsByTagName('src-lang')->item(0)->nodeValue;
                        }

                        //Серия
                        if (empty($title_info->getElementsByTagName('sequence')->item(0))) {
                            $sequence = "яяяяяя"; //Для сортировки
                            $sequence_number = 0;
                        } else {
                            $sequence = trim($title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('name'));
                            if (empty($title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('number'))) {
                                $sequence_number = 0;
                            } else {
                                $sequence_number = $title_info->getElementsByTagName('sequence')->item(0)->GetAttribute('number');
                            }
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

                        $translators_list = $title_info->getElementsByTagName('translator');
                        $translators = '';
                        if (count($translators_list) > 0) {
                            $element = '';
                            foreach ($translators_list as $element) {
                                $translator = array($element->getElementsByTagName('first-name')->item(0)->nodeValue,
                                    $element->getElementsByTagName('last-name')->item(0)->nodeValue,
                                    $element->getElementsByTagName('middle-name')->item(0)->nodeValue);
                                $first_name = trim($translator[0]) ?: "";
                                $last_name = trim($translator[1]) ?: "";
                                $middle_name = trim($translator[2]) ?: "";
                                $translators = $last_name . ' ' . $first_name . ' ' . $middle_name . ', ';
                            }
                            $translators = substr($translators, 0, -2);
                        }

                        //Подгружаем секцию 'document-info'
                        $document_info = $description->getElementsByTagName('document-info')->item(0);
                        //doc authors
                        $authors_list = $document_info->getElementsByTagName('author');
                        if (count($authors_list) > 0) {
                            $element = '';
                            foreach ($authors_list as $element) {
                                $author = array($element->getElementsByTagName('nickname')->item(0)->nodeValue,
                                    $element->getElementsByTagName('email')->item(0)->nodeValue);
                                $nickname = trim($author[0]) ?: "";
                                $email = trim($author[1]) ?: ""; //пока не используется
                                $bk_doc_authors = $nickname . ', ';
                            }
                            $bk_doc_authors = substr($bk_doc_authors, 0, -2);
                        }
                        //program-used
                        $bk_doc_programms = $document_info->getElementsByTagName('program-used')->item(0)->nodeValue;

                        //Дата документа
                        if (empty($document_info->getElementsByTagName('date')->item(0))) {
                            $doc_date = '2099-01-01';
                        } else {
                            $doc_date = $document_info->getElementsByTagName('date')->item(0)->GetAttribute('value');
                            if (!$doc_date || $doc_date == '') {
                                $doc_date = '2099-01-01';
                            }
                        }
                        $doc_time = strtotime($doc_date);

                        //src-ocr
                        $src_ocr = $document_info->getElementsByTagName('src-ocr')->item(0)->nodeValue;

                        //book id
                        $book_id = $document_info->getElementsByTagName('id')->item(0)->nodeValue;

                        //book version
                        $bk_doc_version = $document_info->getElementsByTagName('version')->item(0)->nodeValue;

                        //book src-url
                        $bk_doc_url = $document_info->getElementsByTagName('src-url')->item(0)->nodeValue;

                        //book history
                        if (empty($document_info->getElementsByTagName('history')->item(0))) {
                            $bk_doc_history = '';
                        } else {
                            $bk_doc_history = preg_replace('/\s\s+/', chr(13), trim($document_info->getElementsByTagName('history')->item(0)->nodeValue));
                        }

                        //Подгружаем секцию 'publish-info'
                        $publish_info = $description->getElementsByTagName('publish-info')->item(0);

                        if ($publish_info) {

                            $bk_pub_title = $publish_info->getElementsByTagName('book-name')->item(0)->nodeValue;

                            $bk_pub_publisher = $publish_info->getElementsByTagName('publisher')->item(0)->nodeValue;

                            $bk_pub_city = $publish_info->getElementsByTagName('city')->item(0)->nodeValue;

                            $bk_pub_year = $publish_info->getElementsByTagName('year')->item(0)->nodeValue;

                            $bk_pub_isbn = $publish_info->getElementsByTagName('isbn')->item(0)->nodeValue;
                            if (stripos($bk_pub_isbn, 'ISBN')) {
                                $bk_pub_isbn = trim(str_replace('ISBN', '', $bk_pub_isbn));
                            }
                        }

                        /********************************** MAIN QUERIES ***********************************************/

                        $stmt = $pdo->prepare('INSERT INTO books (bk_ur_id,
                                                                 bk_se_id,
                                                                 bk_number,
                                                                 bk_added,
                                                                 bk_title,
                                                                 bk_src_title,
                                                                 bk_annotation,
                                                                 bk_date,
                                                                 bk_lang,
                                                                 bk_src_lang,
                                                                 bk_file,
                                                                 bk_cover,
                                                                 bk_keywords,
                                                                 bk_translators,
                                                                 bk_doc_id,
                                                                 bk_doc_authors,
                                                                 bk_doc_programms,
                                                                 bk_doc_date,
                                                                 bk_doc_ocr_authors,
                                                                 bk_doc_ver,
                                                                 bk_doc_url,
                                                                 bk_doc_history,
                                                                 bk_pub_title,
                                                                 bk_pub_publisher,
                                                                 bk_pub_city,
                                                                 bk_pub_year,
                                                                 bk_pub_isbn,
                                                                 bk_doc_file_name,
                                                                 bk_doc_file_size,
                                                                 bk_doc_file_date)
                                                          VALUES ((SELECT ur_id FROM users WHERE ur_login = :login),
                                                                 :id_sequence,
                                                                 :book_number,
                                                                 :bk_added,
                                                                 :book_title,
                                                                 :src_book_title,
                                                                 :book_annotation,
                                                                 :book_date,
                                                                 :book_lang,
                                                                 :book_src_lang,
                                                                 :book_file,
                                                                 :book_cover,
                                                                 :bk_keywords,
                                                                 :bk_translators,
                                                                 :book_id,
                                                                 :bk_doc_authors,
                                                                 :bk_doc_programms,
                                                                 :bk_doc_date,
                                                                 :bk_doc_ocr_authors,
                                                                 :bk_doc_version,
                                                                 :bk_doc_url,
                                                                 :bk_doc_history,
                                                                 :bk_pub_title,
                                                                 :bk_pub_publisher,
                                                                 :bk_pub_city,
                                                                 :bk_pub_year,
                                                                 :bk_pub_isbn,
                                                                 :bk_doc_file_name,
                                                                 :bk_doc_file_size,
                                                                 :bk_doc_file_date);');
                        $stmt->bindValue(':login', $username, PDO::PARAM_STR);
                        $stmt->bindValue(':id_sequence', $id_sequence, PDO::PARAM_INT); //серия
                        $stmt->bindValue(':book_number', $sequence_number, PDO::PARAM_INT); //номер в серии
                        $stmt->bindValue(':bk_added', date('Y-m-d'));
                        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_STR);
                        $stmt->bindValue(':book_title', $book_title, PDO::PARAM_STR);
                        $stmt->bindValue(':src_book_title', $src_book_title, PDO::PARAM_STR);
                        $stmt->bindValue(':book_annotation', $book_annotation, PDO::PARAM_STR);
                        $stmt->bindValue(':book_date', date('Y-m-d', $time));
                        $stmt->bindValue(':book_lang', $book_language, PDO::PARAM_STR);
                        $stmt->bindValue(':book_src_lang', $book_orig_language, PDO::PARAM_STR);
                        $stmt->bindValue(':book_file', $filename, PDO::PARAM_STR);
                        $stmt->bindValue(':book_cover', $cover, PDO::PARAM_LOB);
                        $stmt->bindValue(':bk_keywords', $bk_keywords, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_translators', $translators, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_authors', $bk_doc_authors, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_programms', $bk_doc_programms, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_date', date('Y-m-d', $doc_time));
                        $stmt->bindValue(':bk_doc_ocr_authors', $src_ocr, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_version', $bk_doc_version, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_url', $bk_doc_url, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_history', $bk_doc_history, PDO::PARAM_STR);

                        $stmt->bindValue(':bk_pub_title', $bk_pub_title, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_pub_publisher', $bk_pub_publisher, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_pub_city', $bk_pub_city, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_pub_year', $bk_pub_year, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_pub_isbn', $bk_pub_isbn, PDO::PARAM_STR);

                        $stmt->bindValue(':bk_doc_file_name', $src_filename, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_file_size', $filesize, PDO::PARAM_STR);
                        $stmt->bindValue(':bk_doc_file_date', date('Y-m-d', strtotime($filedate)));

                        //Связанные таблицы
                        if ($stmt->execute()) { //insert
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

                                    $first_name = trim($author[0]) ?: "";
                                    $last_name = trim($author[1]) ?: "";
                                    $middle_name = trim($author[2]) ?: "";
                                    //Поиск автора -
                                    $stmt = $pdo->query("SELECT ar_id FROM authors
                                                                   WHERE ar_first_name = '$first_name' AND ar_last_name = '$last_name' AND ar_middle_name = '$middle_name' ");
                                    $id_author = $stmt->fetchColumn();
                                    //
                                    if (empty($id_author)) {
                                        $stmt = $pdo->exec("INSERT INTO authors (ar_owner, ar_first_name, ar_last_name, ar_middle_name)
                                                                   VALUES ((SELECT ur_id FROM users WHERE ur_login = '$username'), '$first_name', '$last_name', '$middle_name');");
                                        $id_author = $pdo->lastInsertId();
                                    }
                                    //
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
                                "id" => $filename,
                            );

                            /****** Поиск дублей ******/

                            $sql = "select checkDoubles($id_book) result";

                            foreach ($pdo->query($sql) as $row) {
                                $result = $row['result'];
                            }

                            switch ($result) {
                                case 1:
                                    $res["success"] = false;
                                    $res["error"] = "cle";
                                    break;
                            }

                        } else { //конец инсерта
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
        case "del_book":
            if ($pdo and $_SESSION["user"]) {
                $bi = $_POST["dat"];
                $sql = "DELETE FROM books_genres WHERE bkge_bk_id = $bi";
                $cnt = $pdo->exec($sql);
                $sql = "DELETE FROM books_authors WHERE bkar_bk_id = $bi";
                $cnt = $pdo->exec($sql);

                if ($cnt > 0) {
                    $sql = "DELETE FROM books WHERE bk_id = $bi";
                    $cnt1 = $pdo->exec($sql);
                    if ($cnt1 == 0) {
                        $res["success"] = false;
                        $res["error"] = "dbe";
                    } else {
                        $res["data"] = $bi;
                    }
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "check_author":
            if ($pdo and $_SESSION["user"]) {
                $id = $_POST['id'];
                $stmt = $pdo->query("SELECT COUNT(1)
                                     FROM books_authors bkar1,
                                     books_authors bkar2
                                     WHERE bkar1.bkar_ar_id = $id
                                       AND bkar2.bkar_bk_id = bkar1.bkar_bk_id
                                       AND bkar2.bkar_ar_id != $id");
                $count = $stmt->fetchColumn();
                $res["data"] = $count;
            } else {
                $res["success"] = false;
                $res["error"] = "PDO Error";
            }
            break;
        case "del_author":
            if ($pdo and $_SESSION["user"]) {
                $id = $_POST['id'];
                $sql = "select delAuthor($id, '$check') result";
                foreach ($pdo->query($sql) as $row) {
                    $result = $row['result'];
                }
                if ($result) {
                    $res["data"] = $result;
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
            }
            break;
        case "synonym_author":
            if ($pdo and $_SESSION["user"]) {
                $id1 = $_POST['id1'];
                $id2 = $_POST['id2'];
                $sql = "update authors set ar_gr_id = $id2 where ar_id = $id1";
                $cnt = $pdo->exec($sql);
                if ($cnt > 0) {
                    $res["data"] = $cnt;
                } else {
                    $res["success"] = false;
                    $res["error"] = "dbe";
                }
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
