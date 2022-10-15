<?php
require_once('../config.php');
require_once('./db_write.php');

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["userid"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
        $userid = $_POST["userid"];
        try {
            $pdo = db_access();

            $stmt = $pdo->prepare('SELECT * FROM usertable WHERE username = ?');
            $stmt->execute(array($userid));

            $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    // 入力したIDのユーザー名を取得
                    $id = $row['username'];
                    $sql = "SELECT * FROM usertable WHERE username = '$id'";
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['username'];
                    }
                    echo "ログイン成功";
                    exit();
                } else {
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
        }
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>

<body>
    <h1>ログイン画面</h1>
    <form id="loginForm" name="loginForm" action="" method="POST">
        <fieldset>
            <legend>ログインフォーム</legend>
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font>
            </div>
            <label for="userid">ユーザーID</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {
                                                                                                                                echo htmlspecialchars($_POST["userid"], ENT_QUOTES);
                                                                                                                            } ?>">
            <br>
            <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
            <br>
            <input type="submit" id="login" name="login" value="ログイン">
        </fieldset>
    </form>
    <br>
    <form action="SignUp.php">
        <fieldset>
            <legend>ユーザー登録</legend>
            <input type="submit" value="新規登録">
        </fieldset>
    </form>
</body>

</html>