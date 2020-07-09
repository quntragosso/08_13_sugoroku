<?php

// var_dump($_POST);
// exit();

include("functions.php");

if (
    !isset($_POST["username"]) || $_POST["username"] == "" ||
    !isset($_POST["passphrase"]) || $_POST["passphrase"] == ""
) {
    header("Location:sugoroku_startpage.php");
    exit();
}

// データ受け取り
$username = $_POST["username"];
$passphrase = $_POST["passphrase"];
$gold = 1000;
$img = "default";

// DB接続関数
$pdo = connect_to_db();

// ユーザ存在有無確認
$sql = "SELECT COUNT(*) FROM sugoroku_users WHERE username=:username";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(":username", $username, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
}

if ($stmt->fetchColumn() > 0) {
    // user_idが1件以上該当した場合はエラーを表示して元のページに戻る
    // $count = $stmt->fetchColumn();
    echo "<p>このユーザー名は既に登録されているか、使用できません。</p>";
    echo '<a href="sugoroku_startpage.php">login</a>';
    exit();
}

// ユーザ登録SQL作成
$sql = "INSERT INTO sugoroku_users(id, username, passphrase, is_admin, is_deleted, gold, img) VALUES(NULL, :username, :passphrase, 0, 0, :gold, :img)";

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":username", $username, PDO::PARAM_STR);
$stmt->bindValue(":passphrase", $passphrase, PDO::PARAM_STR);
$stmt->bindValue(":gold", $gold, PDO::PARAM_INT);
$stmt->bindValue(":img", $img, PDO::PARAM_STR);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    // ログインできたら情報をsession領域に保存して一覧ページへ移動
    $_SESSION = array(); // セッション変数を空にする
    $_SESSION["session_id"] = session_id();
    $_SESSION["is_admin"] = $val["is_admin"];
    $_SESSION["username"] = $val["username"];
    header("Location:sugoroku_userpage.php");
    exit();
}
