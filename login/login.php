<?php
session_start();
require_once("../config.php");

function login()
{
    if ($_SESSION['user_login'] != true) {
        return false;
    }
    return true;
}

function show_login_form()
{
    echo  '
<h1 class="login_logo">MTTS</h1>
id:yoshida pass:test
<div class="login_box">
    <form method="post" action="index.php">
        <label for="userid">ID</label>
        <input id="userid" type="text" name="userid" value="">
        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" value="">
        <input type="submit" name="login" value="ログイン">
    </form>
</div>';
}

function take_submit()
{
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
                        $_SESSION['user_login'] = true;
                        header('Location: ./');
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

    if (isset($_POST['logout'])) {
        $_SESSION['user_login'] = false;
        $_SESSION = array();
        if (isset($_COOKIE["PHPSESSID"])) {
            setcookie("PHPSESSID", '', time() - 1800, '/');
        }
        session_destroy();
        header('Location: ./');
        exit;
    }
}
