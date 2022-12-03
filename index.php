<?php
include("head.php");
if (private_setting() == false) {
        if (login() == false) {
                echo "<a href='./login/'>ここをクリックしてログインするか、ＨＰを公開に設定してください。</a>";
                return;
        }
}
?>

<body id="top">
        <?php
        include("modal.php");
        include("header.php");
        include("contents.php");
        include("footer.php");
        ?>
</body>

</html>