<?php

include("functions.php");

$game_id = $_POST["game_id"];
$player1 = $_POST["player1"];
$player2 = $_POST["player2"];
$player3 = $_POST["player3"];
$player4 = $_POST["player4"];

$sql = "UPDATE sugoroku_games SET player1=:player1, player2=:player2, player3=:player3, player4=:player4 where game_id=:game_id";

$pdo = connect_to_db();
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$stmt->bindValue(":player1", $player1, PDO::PARAM_STR);
$stmt->bindValue(":player2", $player2, PDO::PARAM_STR);
$stmt->bindValue(":player3", $player3, PDO::PARAM_STR);
$stmt->bindValue(":player4", $player4, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    exit();
};
