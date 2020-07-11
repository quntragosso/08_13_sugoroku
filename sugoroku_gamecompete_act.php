<?php

// 共通処理
session_start();
include("functions.php");
check_session_id();

$game_id = $_POST["game_id"];
$username = $_SESSION["username"];

$sql = "SELECT * from sugoroku_games where game_id = :game_id";
$pdo = connect_to_db();
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
};

// ロードゲームの場合、ゲストであるという情報と合わせて移動。
if ($result[0]["player1"] == $username) {
    $_SESSION["playerNumber"] = "player1";
    $_SESSION["host_or_guest"] = "guest";
    $_SESSION["game_id"] = $game_id;
    header("Location:sugoroku_game.php");
    exit();
} else if ($result[0]["player2"] == $username) {
    $_SESSION["playerNumber"] = "player2";
    $_SESSION["host_or_guest"] = "guest";
    $_SESSION["game_id"] = $game_id;
    header("Location:sugoroku_game.php");
    exit();
} else if ($result[0]["player3"] == $username) {
    $_SESSION["playerNumber"] = "player3";
    $_SESSION["host_or_guest"] = "guest";
    $_SESSION["game_id"] = $game_id;
    header("Location:sugoroku_game.php");
    exit();
} else if ($result[0]["player4"] == $username) {
    $_SESSION["playerNumber"] = "player4";
    $_SESSION["host_or_guest"] = "guest";
    $_SESSION["game_id"] = $game_id;
    header("Location:sugoroku_game.php");
    exit();
}

// 新規ゲームに参加する場合、データベースを更新してから移動。該当なしの場合、userpageに戻される。
if ($result[0]["player2"] == "cpu") {
    $sql_update = "UPDATE sugoroku_games SET player2=:username where game_id=:game_id";
    $_SESSION["playerNumber"] = "player2";
    $_SESSION["host_or_guest"] = "guest";
} else if ($result[0]["player3"] == "cpu") {
    $sql_update = "UPDATE sugoroku_games SET player3=:username where game_id=:game_id";
    $_SESSION["playerNumber"] = "player3";
    $_SESSION["host_or_guest"] = "guest";
} else if ($result[0]["player4"] == "cpu") {
    $sql_update = "UPDATE sugoroku_games SET player4=:username where game_id=:game_id";
    $_SESSION["playerNumber"] = "player4";
    $_SESSION["host_or_guest"] = "guest";
} else {
    header("Location: sugoroku_userpage.php");
    exit();
}

$pdo_update = connect_to_db();
$stmt_update = $pdo_update->prepare($sql_update);
$stmt_update->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$stmt_update->bindValue(":username", $username, PDO::PARAM_STR);
$status_update = $stmt_update->execute();

if ($status_update == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_update->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $_SESSION["game_id"] = $game_id;
    header("Location: sugoroku_game.php");
    exit();
};
