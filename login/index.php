<?php
require_once("login.php");
require_once("db_write.php");
shelfmng_submit();
take_submit();
?>
<!DOCTYPE html>

<head>
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container" id="container">
        <?php if (login()) {
            show_success_message() ?>
            <div class="edit_system">
                <div class="edit_header">
                    <a href="../index.php"><label class="mtts_logo">トップページ</label></a>
                    <label id="cat_clear" class="cat_clear">内容クリア</label>
                    <label id="cat_photo" class="cat_photo">風景記事投稿</label>
                    <label id="cat_works" class="cat_works">事業案内投稿</label>
                    <form method="post" action="index.php">
                        <input type="submit" name="logout" value="ログアウト">
                    </form>
                </div>
                <div class="edit_box">
                    <form id="stkform" method="post" action="index.php" enctype="multipart/form-data">
                        <input id="stk_num" type="hidden" name="stknum" value="" readonly>
                        <label for="stk_cat">カテゴリー</label>
                        <!--<input id="stk_cat" type="text" name="stkcat" value="stockphoto" readonly>-->
                        <select name="stkcat" id="stk_cat">
                            <option value="stockphoto">風景／フォト</option>
                            <option value="works">事業案内</option>
                            <option value="topimagetext" hidden>サムネール文章</option>
                            <option value="toprighttext" hidden>サムネール右側の縦書き文章</option>
                            <option value="mainlefttext" hidden>メインスペース左側</option>
                            <option value="mainrighttext" hidden>メインスペース右側</option>
                        </select>
                        <label for="stk_title">タイトル</label>
                        <input id="stk_title" type="text" name="stktitle" value="">
                        <label for="stk_item">記事</label>
                        <textarea id="stk_item" type="text" name="stkitem" value=""></textarea>
                        <label for="stk_image">イメージ画像</label>
                        <input id="stk_image" type="file" name="stkimage[]" accept="image/*">
                        <input id="stk_imageurl" type="hidden" name="imageurl">
                        <div id="preview"></div>
                        <button class="stksubmit" id="stksubmit" name="stksubmit" value="stksubmit">投稿</button>
                        <button class="delete" id="delete" name="delete" value="delete">削除</button>
                    </form>
                </div>
            </div>
            <div class="edit_menu">
                <h2>投稿した記事一覧</h2>
                <?php
                $pdo = db_access();
                $query = "SELECT * FROM stockphoto;";
                $result = db_prepare_sql($query, $pdo);
                db_close($pdo);

                echo "<select id='item_select' size='20'>";

                $php_array = array();

                foreach ($result as $row) {
                    echo "<option value='" . $row['num'] . "'>" . $row['title'] . "</option>";
                    $php_array[] = array(
                        'num' => $row['num'],
                        'category' => $row['category'],
                        'title' => un_enc($row['title']),
                        'item' => un_enc($row['item']),
                        'imageurl' => $row['imageurl'],
                        'updatetime' => $row['updatetime']
                    );
                }
                echo "</select>";

                $json_array = json_encode($php_array, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                ?>

            </div>
        <?php } else {
            show_login_form();
        } ?>
    </div>
    <script>
        let js_array = <?php echo $json_array; ?>;

        let element = document.getElementById('item_select');
        element.addEventListener('change', function(e) {
            removeImage();
            var result = js_array.find((v) => v.num == element.value);
            document.getElementById('stk_num').value = element.value;
            document.getElementById('stk_cat').value = result['category'];
            document.getElementById('stk_title').value = result['title'];
            document.getElementById('stk_item').value = result['item'];
            var ielem = document.getElementById('stk_imageurl');

            if (result['imageurl'] != "") {
                var elem = document.getElementById('preview');
                elem.style.display = "block";

                var url = "../stock_images/" + result['imageurl'];
                ielem.value = url;
                var img = new Image();
                img.src = url;
                elem.appendChild(img);

                var button = document.createElement('button');
                button.id = "btn";
                button.textContent = "×";
                elem.appendChild(button);
            }
        });

        document.getElementById('stk_image').addEventListener('change', function(e) {
            var elem = document.getElementById('preview');
            elem.style.display = "block";
            for (var num in e.target.files) {
                var file = e.target.files[num];
                var blobUrl = window.URL.createObjectURL(file);
                var img = new Image();
                img.src = blobUrl;
                elem.appendChild(img);

                var button = document.createElement('button');
                button.id = "btn";
                button.textContent = "×";
                elem.appendChild(button);
            }
        });

        document.addEventListener('click', function(e) {
            switch (e.target.id) {
                case "cat_works":
                    form_reset();
                    document.getElementById('stk_num').value = "";
                    var elem = document.getElementById("stk_cat");
                    elem.value = "works";
                    removeImage();
                    break;
                case "cat_photo":
                    form_reset();
                    document.getElementById('stk_num').value = "";
                    var elem = document.getElementById("stk_cat");
                    elem.value = "stockphoto";
                    removeImage();
                    break;
                case "cat_clear":
                    removeImage();
                    form_reset();
                    break;
                case "delete":
                    const formData = new FormData();
                    formData.append(document.getElementById('stk_num'));
                    formData.append(document.getElementById('stk_imageurl'));
                    formData.append(document.getElementById('delete'));
                    fetch('index.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                    break;
                case "submit":
                    const formData2 = new FormData();
                    formData2.append(document.getElementById('stk_num'));
                    formData2.append(document.getElementById('stk_cat'));
                    formData2.append(document.getElementById('stk_title'));
                    formData2.append(document.getElementById('stk_item'));
                    formData2.append(document.getElementById('stk_image'));
                    formData2.append(document.getElementById('stksubmit'));
                    fetch('index.php', {
                            method: 'POST',
                            body: formData2
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                    break;
            }
        });

        function form_reset() {
            var formElement = document.getElementById('stkform');
            formElement.reset();
        }

        document.getElementById("btn").addEventListener("click", function() {
            removeImage();
        });

        function removeImage() {
            var obj = document.getElementById('stk_image');
            obj.value = '';
            var elem = document.getElementById('preview');
            elem.remove();
            var imageElem = document.getElementById('stk_image');
            imageElem.insertAdjacentHTML('afterend', '<div id="preview"></div>');
        }
    </script>
</body>
<?php $_SESSION['submit_success'] = ""; ?>

</html>