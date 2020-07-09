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
        <h2>~フロントもバックも全部やれ~</h2>

        <div id="startpage_top" class="button_boxes">
            <button id="register_button">新規登録</button>
            <button id="login_button">ログイン</button>
        </div>

        <div id="startpage_register" class="button_boxes">
            <form action="sugoroku_register_act.php" method="POST">
                <label>
                    <div class="input_div">
                        username: <input type="text" name="username">
                    </div>
                </label>
                <label>
                    <div class="input_div">
                        password: <input type="text" name="passphrase">
                    </div>
                </label>
                <div>
                    <button>以上の内容で登録</button>
                </div>
            </form>
        </div>

        <div id="startpage_login" class="button_boxes">
            <form action="sugoroku_login_act.php" method="POST">
                <label>
                    <div class="input_div">
                        username:<input type="text" name="username">
                    </div>
                </label>
                <label>
                    <div class="input_div">
                        <label for="passphrase">password:</label><input type="text" name="passphrase">
                    </div>
                </label>
                <div>
                    <button>ログインする</button>
                </div>
                <div id="hidden">
                    <label><input type="radio" name="is_admin" value="0" checked>そのままログイン</label>
                    <label><input type="radio" name="is_admin" value="1">管理者としてログイン</label>
                </div>
            </form>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
    <script>
        // const game_id = JSON.parse(<php echo $json_game_id; ?>)
    </script>
    <script type="text/javascript" src="js/system.js"></script>
</body>

</html>