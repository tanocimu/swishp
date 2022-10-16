<?php
function db_access()
{
    // DB接続情報
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;
    $dbnm = DB_DATABASE;
    $host = DB_HOSTNAME;
    // 接続先DBリンク
    $connect = "mysql:host={$host};dbname={$dbnm}";

    try {
        // DB接続
        $pdo = new PDO($connect, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "<p>DB接続エラー</p>";
        echo $e->getMessage();
        exit();
    }

    return $pdo;
}

function db_close($pdo)
{
    unset($pdo);
}

function db_prepare_sql(string $sql, $pdo)
{
    try {
        // SQL実行
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // 結果の取得
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
    } catch (Exception $e) {
        echo "<p>DB接続エラー</p>";
        echo $e->getMessage();
        exit();
    }

    return $result;
}

function shelfmng_submit()
{
    if (isset($_POST['stksubmit']) && $_POST['stktitle'] != "" && $_POST['stkitem'] != "") {
        $pdo = db_access();
        $imageurl = "";
        $stringtitle = enc($_POST['stktitle']);
        $stringitem = enc($_POST['stkitem']);

        if (!empty($_FILES['stkimage']['tmp_name'][0])) { //ファイルが選択されていれば$imageにファイル名を代入
            for ($i = 0; $i < count($_FILES['stkimage']['name']); $i++) {
                $imageurl = uniqid(mt_rand(), true); //ファイル名をユニーク化
                $imageurl .= '.' . substr(strrchr($_FILES['stkimage']['name'][$i], '.'), 1); //アップロードされたファイルの拡張子を取得
                move_uploaded_file($_FILES['stkimage']['tmp_name'][$i],  DIR_STOCKIMAGES . $imageurl); //imagesディレクトリにファイル保存
            }
        }

        // stk_num空なら記事を新規作成、あればその番号の記事を更新
        if ($_POST['stknum'] == "") {
            $sql = "INSERT INTO stockphoto (num, category, title, item, imageurl, updatetime) VALUES (NULL, '" . $_POST['stkcat'] . "', '" . $stringtitle . "', '" . $stringitem . "', '" . $imageurl . "', current_timestamp());";
            db_prepare_sql($sql, $pdo);
        } else {
            $sql = "UPDATE stockphoto SET title = '" . $stringtitle . "', item = '" . $stringitem . "', imageurl = '" . $imageurl . "' WHERE stockphoto.num = " . $_POST['stknum'];
            db_prepare_sql($sql, $pdo);
        }
        $_SESSION["success"] = "success";
        header('Location: ./');
        exit;
    } else if (isset($_POST['delete']) && $_POST['stknum'] != "") {
        // ファイル削除
        if (file_exists($_POST['imageurl'])) {
            unlink($_POST['imageurl']);
        }

        // DB削除
        $pdo = db_access();
        $sql = "DELETE FROM stockphoto WHERE stockphoto.num = '" . $_POST['stknum'] . "';";
        db_prepare_sql($sql, $pdo);
        $_SESSION["success"] = "delete";
        header('Location: ./');
        exit;
    }
    return;
}

function show_success_message()
{
    if ($_SESSION["success"] == "success") {
        $success_message = "<div class='editjoined' id='editjoined'>記事の編集に成功しました！</div>";
    } elseif ($_SESSION["success"] == "delete") {
        $success_message = "<div class='editjoined' id='editjoined'>記事を削除しました。</div>";
    }
    echo $success_message;
    $_SESSION["success"] = "";
}

function enc($str)
{
    $str = htmlspecialchars($str, ENT_QUOTES);
    return $str;
}

function un_enc($str)
{
    $str = htmlspecialchars_decode($str);
    return $str;
}

function db_item_show($category = null)
{
    $pdo = db_access();
    $query = "SELECT item FROM stockphoto WHERE category = '" . $category . "';";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        echo nl2br(un_enc($row['item']));
    }
}

function db_itembox_show($category)
{
    $pdo = db_access();
    $query = "SELECT * FROM stockphoto WHERE category = '" . $category . "' LIMIT 8;";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        $text = preg_replace('/^\r\n/m', '', (nl2br(un_enc($row['item']))));
        $text = strip_tags($text);
        echo "
        <div class='itembox'>
        <img src='./stock_images/" . nl2br(un_enc($row['imageurl'])) . "' alt='' />
        <p class='title'><a href='#'>" . nl2br(un_enc($row['title'])) . "</a></p>
        <p class='edittime'>" . nl2br(un_enc($row['updatetime'])) . "</p>
        <p class='item'>" . $text . "</p>
        </div>";
    }
}

function db_images_show()
{
    $pdo = db_access();
    $query = "SELECT imageurl FROM stockphoto WHERE category = 'stockphoto' LIMIT 8;";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        echo "<img src='stock_images/" . $row['imageurl'] . "' alt=''>";
    }
}
