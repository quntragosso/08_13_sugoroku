<?php

//共通処理
session_start();
include("functions.php");
check_session_id();

// 送信データ受け取り
$deletetable = $_SESSION["deletetable"];
$id = $_GET["id"];

// DB接続
$pdo = connect_to_db();

// DELETE文を作成&実行
if ($deletetable != "games") {
    $sql = "DELETE from sugoroku_{$deletetable} where id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
} else if ($deletetable == "games") {
    $sql = "DELETE from sugoroku_{$deletetable} where game_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
}

$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    if ($deletetale == "games") {
        $pdo_second = connect_to_db();

        // DELETE文を作成&実行
        $sql_second = "DELETE from sugoroku_cells where id = :id";
        $stmt_second = $pdo_second->prepare($sql_second);
        $stmt_second->bindValue(':id', $id, PDO::PARAM_STR);
        $status_second = $stmt_second->execute();
        if ($status_second == false) {
            // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
            $error = $stmt_second->errorInfo();
            echo json_encode(["error_msg" => "{$error[2]}"]);
            exit();
        } else {
        }
    }

    // 正常にSQLが実行された場合は一覧ページファイルに移動し，一覧ページの処理を実行する
    unset($_SESSION["deletedata"]);
    header("Location:sugoroku_admin.php");
    exit();
}
