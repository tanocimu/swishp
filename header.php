<div class="main_wrapper">
    <header>
        <div class="header_left">
            <div class="description"><?php db_item_show('toprighttext'); ?></div>
            <div class="etclink">
                <dl>
                    <dd class="instagram"><a href="https://www.instagram.com/swiss_mtts/"><img src="images/instagram.png" alt="instagram"></a></dd>
                </dl>
            </div>
        </div>
        <div class="header_right">
            <div id="js-slide">
                <?php db_images_show(); ?>
            </div>
            <div class="header_right_top_back">topback</div>
            <h1><?php db_item_show('topimagetext'); ?></h1>
            <!--<a href="#" class="btn inquiry">Inquiry<label>お問い合わせ</label></a>-->
            <!--<a for="trigger" id="ib6" tabindex="-1" class="btn staff">Recruit<label>スタッフ募集</label></a>-->
            <a href="#service" class="btn profile">Service<label>事業案内</label></a>
            <div class="mask_item">
                <label>お問い合わせ</label>
                <label>スタッフ募集</label>
                <?php db_item_show('recruit'); ?>
            </div>
        </div>
    </header>
</div>