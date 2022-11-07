<?php
include("head.php");
?>

<body>
    <?php
    include("navi.php");
    ?>
    <div class="over_wrapper">
        <section class="stockphoto">
            <h2>風景stock photo</h2>
            <div class="itembox_wrapper">
                <?php db_itembox_show('stockphoto', 100); ?>
            </div>
        </section>
        <br clear="all" />
    </div>
    <br clear="all" />
    <?php
    include("footer.php");
    ?>

</body>

</html>