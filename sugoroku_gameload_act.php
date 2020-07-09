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

// 本当に存在しているかの確認を行い、ホストとして移動。
if ($result[0]["player1"] == $username) {
    $_SESSION["playerNumber"] = "player1";
    $_SESSION["host_or_guest"] = "host";
    $_SESSION["game_id"] = $game_id;
    $_SESSION["game_status"] = "loadgame";
    header("Location:sugoroku_games.php");
    exit();
} else if ($result[0]["player2"] == $username) {
    $_SESSION["playerNumber"] = "player2";
    $_SESSION["host_or_guest"] = "host";
    $_SESSION["game_id"] = $game_id;
    $_SESSION["game_status"] = "loadgame";
    header("Location:sugoroku_games.php");
    exit();
} else if ($result[0]["player3"] == $username) {
    $_SESSION["playerNumber"] = "player3";
    $_SESSION["host_or_guest"] = "host";
    $_SESSION["game_id"] = $game_id;
    $_SESSION["game_status"] = "loadgame";
    header("Location:sugoroku_games.php");
    exit();
} else if ($result[0]["player4"] == $username) {
    $_SESSION["playerNumber"] = "player4";
    $_SESSION["host_or_guest"] = "host";
    $_SESSION["game_id"] = $game_id;
    $_SESSION["game_status"] = "loadgame";
    header("Location:sugoroku_game.php");
    exit();
} else {
    header("Location:sugoroku_userpage.php");
    exit();
}
