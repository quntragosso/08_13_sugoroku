<?php

// 共通処理
session_start();
include("functions.php");
check_session_id();

$username = $_SESSION["username"];

// newgame用に特殊マスのデータをロード。
$sql = "SELECT * from sugoroku_cells where game_id = '00000000'";

$pdo = connect_to_db();
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    $output .= "ステージ数<input type='tel' name='stage' maxlength='2'><br>";
    $output .= "１ステージあたりのマス数<input type='tel' name='each_stagecells' maxlength='4'><br>";
    foreach ($result as $record) {
        $output .= "{$record["comment"]}<input type='tel' name='{$record["status"]}'><br>";
    };
}

// loadgame用に自分が参加しているゲームをロード。
$sql_second = "SELECT * from sugoroku_games where player1 = :username or player2 = :username or player3 = :username or player4 = :username";
$pdo_second = connect_to_db();
$stmt_second = $pdo_second->prepare($sql_second);
$stmt_second->bindValue(":username", $username, PDO::PARAM_STR);
$status_second = $stmt_second->execute();
if ($status_second == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_second->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result_second = $stmt_second->fetchAll(PDO::FETCH_ASSOC);
    $loadgames = "";
    foreach ($result_second as $record_second) {
        $loadgames .= "<input type='radio' name='game_id' value='{$record_second["game_id"]}'>{$record_second["game_id"]}<br>";
    };
}

$hello = "こんにちは、{$username}さん。";

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>すごろく</title>
</head>

<body>
    <div class="wrapper">
        <h1>すごろく</h1>
        <h2><?= $hello ?></h2>

        <div id="userpage_top" class="button_boxes">
            <button id="newgame_button">新しいゲームを作成</button>
            <button id="load_button">ゲームをロード</button>
            <button id="compete_button">ゲームに参加する</button>
        </div>

        <div id="userpage_newgame" class="button_boxes">
            <form action="sugoroku_gamecreate_act.php" method="POST">
                <div>
                    <?= $output ?>
                </div>
                <div>
                    <button>以上の内容で作成</button>
                </div>
            </form>
            <div class="returns"><button>戻る</button></div>
        </div>

        <div id="userpage_load" class="button_boxes">
            <form action="sugoroku_gameload_act.php" method="POST">
                <label>
                    <div class="input_div">
                        <?= $loadgames ?>
                    </div>
                </label>
                <div>
                    <button>ゲームデータをロードする</button>
                </div>
            </form>
            <div class="returns"><button>戻る</button></div>
        </div>

        <div id="userpage_compete" class="button_boxes">
            <form action="sugoroku_gamecompete_act.php" method="POST">
                <label>
                    <div class="input_div">
                        gameID: <input type="tel" name="game_id">
                    </div>
                </label>
                <div>
                    <button>入力したゲームに参加する</button>
                </div>
            </form>
            <div class="returns"><button>戻る</button></div>
        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/system.js"></script>
</body>

</html>