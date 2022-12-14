<?php
function login()
{
    if ($_SESSION['user_name'] == null) {
        return false;
    }
    return $_SESSION['user_name'];
}

function private_setting()
{
    $pdo = db_access();
    $query = "SELECT * from systemtable";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        if ($row['private'] > 0) {
            return true;
        }
    }
    return false;
}

function private_check()
{
    if (private_setting()) {
        echo "checked";
    }
}

function private_checkbox($cheked)
{
    $query = "";
    if ($cheked == "checked") {
        $query = "UPDATE systemtable SET private = '-1' WHERE systemtable.num = 1";
    } else {
        $query = "UPDATE systemtable SET private = '1' WHERE systemtable.num = 1";
    }

    $pdo = db_access();
    db_prepare_sql($query, $pdo);
    db_close($pdo);
}

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
        $maximage = 5; // アップロード出来る枚数の上限
        $imageurl = "";
        $images = "";
        $imageArray = [];
        $stringtitle = enc($_POST['stktitle']);
        $stringitem = enc($_POST['stkitem']);
        $stringitem = $_POST['stkitem'];

        if (!empty($_FILES['stkimage']['tmp_name'][0])) { //ファイルが選択されていれば$imageにファイル名を代入
            for ($i = 0; $i < count($_FILES['stkimage']['name']); $i++) {
                $imageurl = uniqid(mt_rand(), true); //ファイル名をユニーク化
                $imageurl .= '.' . substr(strrchr($_FILES['stkimage']['name'][$i], '.'), 1); //アップロードされたファイルの拡張子を取得

                $images .= $imageurl . ",";
                array_push($imageArray, $imageurl);

                $str = "@" . date('Y') . " MOUNTAIN TOP TOURIST SERVICES.";
                imageResizeAndWritetext($_FILES['stkimage']['tmp_name'][$i], $imageurl, $str);
                //move_uploaded_file($_FILES['stkimage']['tmp_name'][$i],  DIR_STOCKIMAGES . $imageurl); //imagesディレクトリにファイル保存

                if ($i >= $maximage - 1) {
                    break;
                }
            }
        }

        // stk_num空なら記事を新規作成、あればその番号の記事を更新
        if ($_POST['stknum'] == "") {
            $sql = "INSERT INTO stockphoto (num, category, title, item, imageurl, updatetime) VALUES (NULL, '" . $_POST['stkcat'] . "', '" . $stringtitle . "', '" . $stringitem . "', '" . $images . "', current_timestamp());";
            db_prepare_sql($sql, $pdo);
        } else {
            if ($images == "" && $_POST['imageurl'] != "") {
                $images = $_POST['imageurl'];
            }
            $sql = "UPDATE stockphoto SET title = '" . $stringtitle . "', item = '" . $stringitem . "', imageurl = '" . $images . "' WHERE stockphoto.num = " . $_POST['stknum'];
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
    } else if (isset($_POST['onoff'])) {
        private_checkbox($_POST['onoff']);
    }
    return;
}

// 画像のリサイズと文字入れ
function imageResizeAndWritetext($filename, $rename, $text)
{
    move_uploaded_file($filename, DIR_STOCKIMAGES . $rename); // tmpファイルを画像ファイルにして保存

    $file_pass = DIR_STOCKIMAGES . $rename;

    $new_image = "";
    if (file_exists($file_pass) && $type = exif_imagetype($file_pass)) {
        switch ($type) {
            case IMAGETYPE_GIF:
                $new_image = imagecreatefromgif($file_pass);
                break;
            case IMAGETYPE_JPEG:
                $new_image = imagecreatefromjpeg($file_pass);
                break;
            case IMAGETYPE_PNG:
                $new_image = imagecreatefrompng($file_pass);
                break;
            default:
                echo "画像ファイルではない";
                return;
        }
    }

    $size = getimagesize($file_pass);
    $re_size = imageSizeStretch($size, 1080);

    // 画像のリサイズ
    $stretch_image = imagecreatetruecolor($re_size[0], $re_size[1]);
    imagecopyresampled($stretch_image, $new_image, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

    // 画像に文字を挿入
    if ($text != "") {
        $font = __DIR__ . '/meiryob.woff';
        //$white = imagecolorallocate($stretch_image, 255, 255, 255);
        $alpha = imagecolorallocatealpha($stretch_image, 255, 255, 255, 100);

        $size = 27;
        $angle = 0;
        $x = 10;         // 左からの座標(ピクセル)
        $y = 10 + $size; // 上からの座標(ピクセル)
        $line_height   = 1;

        imagefttext(
            $stretch_image,     // 挿入先の画像
            $size,      // フォントサイズ
            $angle,     // 文字の角度
            $x,         // 挿入位置 x 座標
            $y,         // 挿入位置 y 座標
            $alpha,     // 文字の色
            $font,  // フォントファイル
            $text,      // 挿入文字列
            ['linespacing' => $line_height]
        );
    }

    $upload_dir    = DIR_STOCKIMAGES; // 保存ディレクトリ

    switch ($type) {
        case IMAGETYPE_GIF:
            imagegif($stretch_image, $upload_dir . $rename);
            break;
        case IMAGETYPE_JPEG:
            imagejpeg($stretch_image, $upload_dir . $rename);
            break;
        case IMAGETYPE_PNG:
            imagepng($stretch_image, $upload_dir . $rename);
            break;
        default:
            echo "画像ファイルではない";
            return;
    }

    imagedestroy($new_image);
    imagedestroy($stretch_image);
}

function imageSizeStretch($size, $newWidth)
{
    // 横幅を指定して画像を伸縮
    $re_size[0] = $newWidth;
    $ratio = $newWidth / $size[0]; // 横幅
    $re_size[1] = floor($size[1] * $ratio);

    return $re_size;
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
    $query = "SELECT item, num, imageurl, updatetime, category FROM stockphoto WHERE category = '" . $category . "';";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        if (login()) {
            echo "<lable id='et{$row['num']}' class='edit_fixed'>…</lable>";
        }

        $text = nl2br(un_enc($row['item']));
        $contents = "
        <p id='ibitem{$row['num']}' class='item'>{$text}</p>
        <div id='ibcat{$row['num']}' class='category_hide'>{$row['category']}</div>";

        if ($row['imageurl'] != "") {
            $contents .= "<img id='ibimage{$row['num']}' src='./stock_images/{$row['imageurl']}'><label id='edittime{$row['num']}'>{$row['updatetime']}</label>";
        }

        echo $contents;
    }
}

function db_itembox_show($category, $maxitem, $status = 0)
{
    $pdo = db_access();
    $query = "SELECT * FROM stockphoto WHERE category = '$category' ORDER BY stockphoto.updatetime DESC LIMIT $maxitem;";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        //$text = preg_replace('/^\r\n/m', '', (nl2br(un_enc($row['item']))));
        //$text = strip_tags($text);
        $text = nl2br(un_enc($row['item']));
        $imageArray = explode(',', $row['imageurl']);
        $images = "";
        for ($i = 0; $i < count($imageArray); $i++) {
            if ($imageArray[$i] == "") break;
            $images .= $imageArray[$i] . ",";
        }

        $contents = "
        <div class='itembox'>
        <a class='itembox_mask' for='trigger' id='ib{$row['num']}' tabindex='-1'>" . $images . "</a>
        <img id='ibimage{$row['num']}' src='./stock_images/" . $imageArray[0] . "' alt='' />";
        if ($status == 1) {
            $contents .= "<p class='title'><label id='ibtitle{$row['num']}'>" . nl2br(un_enc($row['title'])) . "</label></p>";
        }

        if (login()) {
            $edit = "<lable id='et{$row['num']}' class='edit'>…</lable>";
        }

        $contents .= "<p class='edittime' id='edittime{$row['num']}'>" . nl2br(un_enc($row['updatetime'])) . "</p>{$edit}
        <p id='ibitem{$row['num']}' class='item'>{$text}</p><div id='ibcat{$row['num']}' class='category'>{$row['category']}</div></div>";

        echo $contents;
    }
}

function db_images_show()
{
    $pdo = db_access();
    $query = "SELECT imageurl FROM stockphoto WHERE category = 'stockphoto' ORDER BY stockphoto.updatetime DESC LIMIT 8 ;";
    $result = db_prepare_sql($query, $pdo);
    db_close($pdo);

    foreach ($result as $row) {
        $imageArray = explode(',', $row['imageurl']);
        echo "<img src='stock_images/" . $imageArray[0] . "' alt=''>";
    }
}
