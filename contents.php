<div class="over_wrapper white">
    <section class="infomation">
        <div class="info_left">
            <?php db_item_show('mainlefttext'); ?>
        </div>
        <div class="info_right">
            <?php db_item_show('mainrighttext'); ?>
        </div>
    </section>
    <br clear="all" />
</div>
<br clear="all" />
<div class="over_wrapper">
    <section class="stockphoto">
        <h2>風景stock photo</h2>
        <div class="itembox_wrapper">
            <?php db_itembox_show('stockphoto');?>
        </div>
    </section>
    <br clear="all" />
</div>
<br clear="all" />
<div class="over_wrapper_2 white">
    <section class="service">
        <h2>事業案内Service</h2>
        <div class="itembox_wrapper">
        <?php db_itembox_show('works');?>
        </div>
    </section>
    <br clear="all" />
</div>
<div class="over_wrapper">
    <section class="access">
        <?php include_once("access.php"); ?>
    </section>
    <br clear="all" />
</div>