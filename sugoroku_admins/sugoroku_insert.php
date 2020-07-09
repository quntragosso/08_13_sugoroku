<?php

//共通処理
session_start();
include("functions.php");
check_session_id();

$insert_content = $_POST["insert_content"];
$content_name = $_POST["content_name"];

$image = ($_FILES["image"]["name"] != "") ? file_get_contents($_FILES["image"]["tmp_name"]) : "";

if ($insert_content == "cells") {
    $game_id = "00000000";

    $content_comment = isset($_POST["content_comment"]) ? $_POST["content_comment"] : exit();

    $sql = "INSERT INTO sugoroku_cells(id, game_id, cell_number, status, comment) VALUES(NULL, :game_id, NULL, :content_name, :content_comment)";

    $pdo = connect_to_db();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":game_id", $game_id, PDO::PARAM_STR);
    $stmt->bindValue(":content_name", $content_name, PDO::PARAM_STR);
    $stmt->bindValue(":content_comment", $content_comment, PDO::PARAM_STR);
    $status = $stmt->execute();

    // データ登録処理後
    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        header("Location:sugoroku_admin.php");
        exit();
    };
} else if ($insert_content == "images") {
    $sql = "INSERT INTO sugoroku_images(id, img, img_name) VALUES(NULL, :img, :img_name)";

    $pdo = connect_to_db();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":img", $image, PDO::PARAM_STR);
    $stmt->bindValue(":img_name", $content_name, PDO::PARAM_STR);
    $status = $stmt->execute();

    // データ登録処理後
    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        header("Location:sugoroku_admin.php");
        exit();
    };
};
