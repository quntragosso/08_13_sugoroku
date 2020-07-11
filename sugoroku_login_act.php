<?php

// 共通処理
session_start();
include("functions.php");
check_session_id();

if (
    !$_POST["username"] || $_POST["username"] == "" ||
    !$_POST["passphrase"] || $_POST["passphrase"] == ""
) {
    header("Location:sugoroku_startpage.php");
    exit();
}

// DB接続します
$pdo = connect_to_db();

// データ受け取り
$username = $_POST["username"];
$passphrase = $_POST["passphrase"];
$is_admin = $_POST["is_admin"];

if ($is_admin == 0) {
    // データ取得SQL作成&実行
    $sql = "SELECT * from sugoroku_users where username = :username AND passphrase = :passphrase AND is_deleted = 0";

    // SQL実行時にエラーがある場合はエラーを表示して終了
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->bindValue(":passphrase", $passphrase, PDO::PARAM_STR);
    $status = $stmt->execute();

    // うまくいったらデータ（1レコード）を取得
    $val = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザ情報が取得できない場合はメッセージを表示
    if (!$val) {
        echo "<p>ログイン情報に誤りがあります。</p>";
        echo '<a href="sugoroku_startpage.php">login</a>';
        exit();
    } else {
        // ログインできたら情報をsession領域に保存して一覧ページへ移動
        $_SESSION = array(); // セッション変数を空にする
        $_SESSION["session_id"] = session_id();
        $_SESSION["username"] = $username;
        header("Location:sugoroku_userpage.php");
        exit();
    }
} else if ($is_admin == 1) {
    header("Location:sugoroku_admins/sugoroku_admin.php");
}
