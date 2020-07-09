<?php

// 共通処理
session_start();
include("functions.php");
check_session_id();

$username = $_SESSION["username"];
$game_id = $_SESSION["game_id"];
$host_or_guest = $_SESSION["host_or_guest"];
$player_number = $_SESSION["playerNumber"];
$game_status = isset($_SESSION["game_status"]) ? $_SESSION["game_status"] : "";

// gameをロード。
$sql_games = "SELECT * from sugoroku_games where game_id = :game_id";

$pdo_games = connect_to_db();
$stmt_games = $pdo_games->prepare($sql_games);
$stmt_games->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$status_games = $stmt_games->execute();

if ($status_games == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_games->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result_games = $stmt_games->fetchAll(PDO::FETCH_ASSOC);
};



// cellをロード。
$sql_cells = "SELECT * from sugoroku_cells where game_id = :game_id order by cell_number asc";

$pdo_cells = connect_to_db();
$stmt_cells = $pdo_cells->prepare($sql_cells);
$stmt_cells->bindValue(":game_id", $game_id, PDO::PARAM_STR);
$status_cells = $stmt_cells->execute();

if ($status_cells == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_cells->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result_cells = $stmt_cells->fetchAll(PDO::FETCH_ASSOC);
};



// 特殊コマのデータをロード
$sql_comment = "SELECT * from sugoroku_cells where game_id = '00000000'";

$pdo_comment = connect_to_db();
$stmt_comment = $pdo_comment->prepare($sql_comment);
$status_comment = $stmt_comment->execute();

if ($status_comment == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt_comment->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result_comment = $stmt_comment->fetchAll(PDO::FETCH_ASSOC);
    $comment_array = array("start" => "スタート");
    $comment_array += array("goal" => "ゴール");
    $comment_array += array("none" => "");
    foreach ($result_comment as $record) {
        $comment_array += array($record["status"] => $record["comment"]);
    };
};

$players = "<div id='player1' class='player_div'>player1:{$result_games[0]['player1']}</div>";
$players .= "<div id='player2' class='player_div'>player2:{$result_games[0]['player2']}</div>";
$players .= "<div id='player3' class='player_div'>player3:{$result_games[0]['player3']}</div>";
$players .= "<div id='player4' class='player_div'>player4:{$result_games[0]['player4']}</div>";


$cells = "";
for ($j = 1; $j <= $result_games[0]["stage"]; $j++) {
    $cells .= "<div id='stage{$j}' class='stages'><table><tr>";
    for ($i = 0; $i < count($result_cells); $i++) {
        if ($result_cells[$i]["stage_number"] == $j) {
            $cells .= "<td id='{$result_cells[$i]["cell_number"]}' class='{$result_cells[$i]["status"]}'>{$comment_array[$result_cells[$i]["status"]]}</td>";
        }
    }
    $cells .= "</tr></table></div>";
}

$announce = "gameIDは{$game_id}です。";
// 各種変数をjsに送る処理。
$json_game_id = json_encode($game_id, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json_username = json_encode($username, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json_hostorguest = json_encode($host_or_guest, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json_playernumber = json_encode($player_number, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json_gamestatus = json_encode($game_status, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gamestyle.css">
    <title>すごろく</title>
</head>

<body>
    <div class="wrapper">
        <div id="players" class="divs">
            <?= $players ?>
        </div>
        <div id="game_cells" class="divs">
            <div id="newgame_top" class="tops">
                <button id="game_start">このメンバーで始める</button>
                <?= $announce ?>
            </div>
            <div id="loadgame_top" class="tops">
                <button id="game_restart">ゲームを再開する</button>
                <?= $announce ?>
            </div>
            <div id="waitgame_top" class="tops">
                <div>ホストの開始を待っています。</div>
            </div>
            <?= $cells ?>
        </div>
        <div id="information" class="divs">
            <div id="dice" class="untouchable">ダイスを振る</div>
            <div id="info_box"></div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.15.5/firebase.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

    <script>
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyBwWqKEF01svLdC61o0zz7pvz5PeboeFZc",
            authDomain: "the-sugoroku.firebaseapp.com",
            databaseURL: "https://the-sugoroku.firebaseio.com",
            projectId: "the-sugoroku",
            storageBucket: "the-sugoroku.appspot.com",
            messagingSenderId: "882950174274",
            appId: "1:882950174274:web:57c90f76ad6bb9e026dfdb"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        let games_server = firebase.firestore().collection("games_server");
    </script>
    <script>
        const gameID = JSON.parse('<?php echo $json_game_id; ?>');
        const username = JSON.parse('<?php echo $json_username; ?>');
        const hostOrGuest = JSON.parse('<?php echo $json_hostorguest; ?>');
        const playerNumber = JSON.parse('<?php echo $json_playernumber; ?>');
        const gameStatus = JSON.parse('<?php echo $json_gamestatus; ?>');
    </script>
    <script type="text/javascript" src="js/system2.js"></script>
</body>

</html>