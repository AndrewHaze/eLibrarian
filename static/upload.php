<?php
//Включение буферизации вывода
//что-бы не портить JSON
ob_start(); 
require_once "functions.php";

set_cors();

session_name("xeg6joZqNSGP3FyEi6xW");
session_start();

$res = array("data" => array(), "success" => true, "error" => "");

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir);
}

$name = basename($_FILES["file"]["name"]);

$new_name = uniqid($_SESSION["user"] . "_", true) . "." . pathinfo($name, PATHINFO_EXTENSION);

$target_file = $target_dir . $new_name;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file) && ($_SESSION["user"] != null)) {
    $res["data"] = array(
        "base_name" => $name,
        "hash_name" => $new_name,
        "status" => "raw",
    );
} else {
    $res["success"] = false;
    $res["error"] = "I/O Error (File Upload)";
}
ob_end_clean(); //Чистим буфер
echo json_encode($res);
