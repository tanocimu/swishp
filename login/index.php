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
                    <form class="switchArea" action="index.php" method="post" name="switchArea">
                        <input type="checkbox" id="switch1" name="switch1" <?php private_check(); ?>>
                        <label for="switch1"><span></span></label>
                        <div id="swImg"></div>
                        <input type="hidden" id="onoff" name="onoff" value="<?php private_check(); ?>">
                    </form>
                    <a href="../index.php"><label class="mtts_logo">トップページ</label></a>
                    <label id="cat_clear" class="cat_clear">クリア</label>
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
                        <div class='stk_title_box' id='stk_title_box'>
                            <label for="stk_title">タイトル</label>
                            <input id="stk_title" type="text" name="stktitle" value="">
                        </div>
                        <label for="stk_item">記事</label>
                        <textarea id="stk_item" type="text" name="stkitem" value=""></textarea>
                        <label for="stk_image">イメージ画像</label>
                        <input id="stk_image" type="file" name="stkimage[]" accept="image/*" multiple>
                        <input id="stk_imageurl" type="hidden" name="imageurl" multiple>
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

        let stk_itemelem = document.getElementById('stk_item');
        let stk_catelm = document.getElementById('stk_cat');

        document.getElementById('switch1').addEventListener('change', function(e) {
            document.forms.switchArea.submit();
            const formData = new FormData();
            formData.append(document.getElementById('onoff'));
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
        });

        stk_itemelem.addEventListener('blur', function(e) {
            if (stk_catelm.value == "stockphoto") {
                let stk_titleelem = document.getElementById('stk_title');
                stk_titleelem.value = stk_itemelem.value.slice(0, 12);
            }
        });

        stk_catelm.addEventListener('change', function(e) {
            let stk_title_boxelem = document.getElementById('stk_title_box');

            if (stk_catelm.value == "works") {
                stk_title_boxelem.style.display = "block";
            }
            if (stk_catelm.value == "stockphoto") {
                stk_title_boxelem.style.display = "none";
            }
        });

        let element = document.getElementById('item_select');
        element.addEventListener('change', function(e) {
            resetPreview();
            var result = js_array.find((v) => v.num == element.value);
            document.getElementById('stk_num').value = element.value;
            document.getElementById('stk_cat').value = result['category'];
            document.getElementById('stk_title').value = result['title'];
            document.getElementById('stk_item').value = result['item'];
            var ielem = document.getElementById('stk_imageurl');

            if (result['imageurl'] != "") {
                var imageArray = result['imageurl'].split(',');

                var elem = document.getElementById('preview');
                elem.style.display = "block";

                for (var index = 0; index < imageArray.length; index++) {
                    if (imageArray[index] == "") break;
                    // var imgDiv = document.createElement('div');
                    //  imgDiv.className = "imageDiv";

                    var url = "../stock_images/" + imageArray[index];
                    var img = new Image();
                    img.src = url;
                    elem.appendChild(img);

                    ielem.value += url + ",";

                    var button = document.createElement('a');
                    button.id = "btn";
                    button.textContent = "×";
                    elem.appendChild(button);

                    // imgDiv.appendChild(img);
                    //  imgDiv.appendChild(button);
                    //  elem.appendChild(imgDiv);
                }
            }
        });

        document.getElementById('stk_image').addEventListener('change', function(e) {
            resetPreview();
            var elem = document.getElementById('preview');
            elem.style.display = "block";
            for (var num in e.target.files) {
                var file = e.target.files[num];
                var blobUrl = window.URL.createObjectURL(file);
                var img = new Image();
                img.src = blobUrl;
                elem.appendChild(img);

                var button = document.createElement('a');
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
                    resetPreview();
                    break;
                case "cat_photo":
                    form_reset();
                    document.getElementById('stk_num').value = "";
                    var elem = document.getElementById("stk_cat");
                    elem.value = "stockphoto";
                    resetPreview();
                    break;
                case "cat_clear":
                    resetPreview();
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
                case "btn":
                    resetPreview();
                    break;
            }
        });

        function form_reset() {
            var formElement = document.getElementById('stkform');
            formElement.reset();
        }

        function resetPreview() {
            var element = document.getElementById("preview");
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }

            let imagelm = document.getElementById('stk_imageurl');
            imagelm.value = "";
        }
    </script>
</body>
<?php $_SESSION['submit_success'] = ""; ?>

</html>