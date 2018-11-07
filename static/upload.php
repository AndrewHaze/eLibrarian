<?php

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

$target_file = $target_dir . uniqid($_SESSION["user"] . "_", true) . "." . pathinfo($name, PATHINFO_EXTENSION);

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file) && ($_SESSION["user"] != null)) {
    $res["data"] = array(
        "base_name" => $name,
        "hash_name" => $target_file,
        "status" => "raw",
    );
} else {
    $res["success"] = false;
    $res["error"] = "I/O Error (File Upload)";
}

echo json_encode($res);
