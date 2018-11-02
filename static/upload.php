<?php

require_once('functions.php'); 

set_cors();

$res = array("data" => array(), "success" => true, "error" => "");

$target_dir = "uploads/";
if(!file_exists($target_dir)){
    mkdir($target_dir);
}

$name = basename($_FILES["file"]["name"]);

$target_file = $target_dir . uniqid('bk_', true) . '.' . pathinfo($name, PATHINFO_EXTENSION); 

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    $res["data"] =  array(
        "base_name" => $name,
        "hash_name" => $target_file,
        "status" => "raw",
    );
} else {
    $res["data"] =  array(
        "base_name" => $name,
        "hash_name" => $target_file,
        "status" => "err",
    );
}


echo json_encode($res);
