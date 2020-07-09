<?php

//共通処理
session_start();
include("functions.php");
check_session_id();

// $username = $_session["username"];
$username = "QunQuuun";

$hello = "こんにちは{$username}さん。";

$readdata = isset($_POST["readdata"]) ? $_POST["readdata"] : "none";
$_SESSION["deletetable"] = isset($_POST["readdata"]) ? $readdata : "";

if ($readdata != "none") {
    $sql = "SELECT * from sugoroku_{$readdata}";

    if ($readdata == "cells") {
        $sql .= " where game_id = '00000000'";
    }

    $pdo = connect_to_db();
    $stmt = $pdo->prepare($sql);
    $status = $stmt->execute();

    if ($status == false) {
        $error = $stmt->errorInfo();
        echo json_encode(["error_msg" => "{$error[2]}"]);
        exit();
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    };
};

$thead = "";
$output = "";

if ($readdata == "users") {
    $thead = "<tr><td>username</td><td>is_admin</td><td>is_deleted</td><td>gold</td><td>img</td></tr>";
    foreach ($result as $record) {
        $output .= "<tr>";
        $output .= "<td>{$record["username"]}</td>";
        $output .= "<td>{$record["is_admin"]}</td>";
        $output .= "<td>{$record["is_deleted"]}</td>";
        $output .= "<td>{$record["gold"]}</td>";
        $output .= "<td>{$record["img"]}</td>";
        $output .= "<td><a href='sugoroku_delete.php?id={$record["id"]}'>完全に消去する</a></td>";
        $output .= "</tr>";
    };
} else if ($readdata == "games") {
    $thead = "<tr><td>game_id</td><td>player1</td><td>player2</td><td>player3</td><td>player4</td><td>nowplayer</td><td>finalsave</td></tr>";
    foreach ($result as $record) {
        $output .= "<tr>";
        $output .= "<td>{$record["game_id"]}</td>";
        $output .= "<td>{$record["player1"]}</td>";
        $output .= "<td>{$record["player2"]}</td>";
        $output .= "<td>{$record["player3"]}</td>";
        $output .= "<td>{$record["player4"]}</td>";
        $output .= "<td>{$record["nowplayer"]}</td>";
        $output .= "<td>{$record["finalsave"]}</td>";
        $output .= "<td><a href='sugoroku_delete.php?id={$record["game_id"]}'>完全に消去する</a></td>";
        $output .= "</tr>";
    };
} else if ($readdata == "cells") {
    $thead = "<tr><td>status</td><td>comment</td></tr>";
    foreach ($result as $record) {
        $output .= "<tr>";
        $output .= "<td>{$record["status"]}</td>";
        $output .= "<td>{$record["comment"]}</td>";
        $output .= "<td><a href='sugoroku_delete.php?id={$record["id"]}'>完全に消去する</a></td>";
        $output .= "</tr>";
    };
} else if ($readdata == "images") {
    $thead = "<tr><td>img</td><td>img_name</td></tr>";
    foreach ($result as $record) {
        $output .= "<tr>";
        $output .= "<td><img src='{$record["img"]}'}</td>";
        $output .= "<td>{$record["img_name"]}</td>";
        $output .= "<td><a href='sugoroku_delete.php?id={$record["id"]}'>完全に消去する</a></td>";
        $output .= "</tr>";
    };
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>すごろく管理ページ</title>
    <style>
        * {
            margin: 5px;
        }

        #top {
            display: flex;
            justify-content: start;
            align-items: center;
        }

        #input_space {
            display: none;
        }
    </style>
</head>

<body>
    <div id="top">
        <table>
            <tbody><?= $hello ?></tbody>
        </table>
        <form action="sugoroku_admin.php" method="POST">
            <label><input type="radio" name="readdata" value="users" checked>ユーザー一覧</label>
            <label><input type="radio" name="readdata" value="games">ゲーム一覧</label>
            <label><input type="radio" name="readdata" value="cells">コマ一覧</label>
            <label><input type="radio" name="readdata" value="images">アバター一覧</label>
            <button>検索</button>
        </form>

        <button id="add">コンテンツの追加</button>
    </div>

    <div id="output_space">
        <table>
            <thead><?= $thead ?></thead>
            <tbody><?= $output ?></tbody>
        </table>
    </div>
    <div id="input_space">
        <form action="sugoroku_insert.php" method="POST" enctype="multipart/form-data">
            <input type="radio" name="insert_content" value="cells" checked>コマ
            <input type="radio" name="insert_content" value="images">アバター<br>
            name<input type="text" maxlength="64" name="content_name">
            comment<input type="text" maxlength="64" name="content_comment">
            <input type="file" name="image">
            <button>登録</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#add").on("click", function() {
                $("#output_space").css("display", "none");
                $("#input_space").css("display", "flex");
            });
        });
    </script>
</body>

</html>