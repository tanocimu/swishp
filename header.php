<div class="main_wrapper">
    <header>
        <div class="header_left">
            <div class="description"><?php db_item_show('toprighttext'); ?></div>
            <div class="etclink">
                <dl>
                    <dd class="google_map"><a href="#access"><img src="images/google_map.png" alt="access"></a></dd>
                    <dd class="twitter"><a href="#"><img src="images/twitter.png" alt="twitter"></a></dd>
                    <dd class="instagram"><a href="#"><img src="images/instagram.png" alt="instagram"></a></dd>
                </dl>
            </div>
        </div>
        <div id="js-slide" class="header_right">
            <?php db_images_show(); ?>
            <div class="header_right_top_back">topback</div>
            <h1><?php db_item_show('topimagetext'); ?></h1>
            <a href="#" class="btn inquiry">Inquiry<label>お問い合わせ</label></a>
            <a href="#" class="btn staff">Recruit<label>スタッフ募集</label></a>
            <a href="#service" class="btn profile">Service<label>事業案内</label></a>
        </div>
    </header>
</div>