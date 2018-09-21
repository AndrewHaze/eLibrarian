<?php
$authors = array(
	array(
		"id" => 1,
        "books" => 1,
        "author" => "Акунин",
		"isActive" => false,
		"_rowVariant" => ""
	),
	array(
		"id" => 2,
        "books" => 2,
        "author" => "Бушков",
		"isActive" => false,
		"_rowVariant" => ""
	),
	array(
		"id" => 3,
        "books" => 3,
        "author" => "Пехов",
		"isActive" => false,
		"_rowVariant" => ""
	)
);
echo json_encode($authors);