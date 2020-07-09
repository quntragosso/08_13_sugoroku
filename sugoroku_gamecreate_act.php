<?php

// 共通処理
session_start();
include("functions.php");
check_session_id();

$username = $_SESSION["username"];

$each_stagecells = $_POST["each_stagecells"];
unset($_POST["each_stagecells"]);
$stage = $_POST["stage"];
unset($_POST["stage"]);

$values_sum = 0;
foreach ($_POST as $key => $value) {
    if (!isset($value) != "" || $value != 0) {
        $values_sum += $value;
    };
};

if ($each_stagecells - 2 < $values_sum) {
    header("Location: sugoroku_userpage.php");
}

$game_id = "";
$a = rand(1, 9);
$b = rand(0, 9);
$c = rand(0, 9);
$d = rand(0, 9);
$e = rand(0, 9);
$f = rand(0, 9);
$g = rand(0, 9);
$h = rand(0, 9);
$newArray = [$a, $b, $c, $d, $e, $f, $g, $h];
for ($i = 0; $i < count($newArray); $i++) {
    $game_id .= (string) $newArray[$i];
};

$cpu = "cpu";

$sql_togames = "INSERT INTO sugoroku_games(id, game_id, stage, player1, player2, player3, player4, nowplayer, finalsave) VALUES(NULL, :game_id, :stage,:username, :cpu, :cpu, :cpu, 'player1', sysdate())";
$sql_tocells = "INSERT INTO sugoroku_cells(id, game_id, cell_number, stage_number ,status, comment) VALUES";

// stage分だけマスの生成を繰り返す。
for ($l = 1; $l <= $stage; $l++) {
    $cells_array = [];
    for ($i = 0; $i < $each_stagecells; $i++) {
        array_push($cells_array, "none");
    };

    array_splice($cells_array, 0, 1, "start");
    array_splice($cells_array, count($cells_array) - 1, 1, "goal");

    foreach ($_POST as $key => $value) {
        if (!isset($value) != "" || $value != 0) {
            for ($j = 0; $j < $value;) {
                $cell_order = rand(1, $each_stagecells) - 1;
                if ($cells_array[$cell_order] == "none") {
                    array_splice($cells_array, $cell_order, 1, $key);
                    $j++;
                }
            }
        };
    }

    for ($k = 0; $k < count($cells_array); $k++) {
        if ($l == $stage && $k == count($cells_array) - 1) {
            $sql_tocells .= "(NULL, :game_id, $k, $l, '{$cells_array[$k]}', NULL)";
        } else {
            $sql_tocells .= "(NULL, :game_id, $k, $l, '{$cells_array[$k]}', NULL),";
        }
    };
};
// var_dump($sql_togames);
// exit();

// sugoroku_gamesへのINSERT
$pdo_togames = connect_to_db();
$stmt_togames = $pdo_togames->prepare($sql_togames);
$stmt_togames->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$stmt_togames->bindValue(":stage", $stage, PDO::PARAM_INT);
$stmt_togames->bindValue(":username", $username, PDO::PARAM_STR);
$stmt_togames->bindValue(":cpu", $cpu, PDO::PARAM_STR);
$status_togames = $stmt_togames->execute();

if ($status_togames == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_togames->errorInfo();
    echo json_encode(["error_msg1" => "{$error[2]}"]);
    exit();
} else {
};

// sugoroku_cellsへのINSERT
$pdo_tocells = connect_to_db();
$stmt_tocells = $pdo_tocells->prepare($sql_tocells);
$stmt_tocells->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$status_tocells = $stmt_tocells->execute();

if ($status_tocells == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_tocells->errorInfo();
    echo json_encode(["error_msg2" => "{$error[2]}"]);
    exit();
} else {
    $_SESSION["game_id"] = $game_id;
    $_SESSION["playerNumber"] = "player1";
    $_SESSION["host_or_guest"] = "host";
    $_SESSION["game_status"] = "newgame";
    header("Location:sugoroku_game.php");
};
