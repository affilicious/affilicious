<?php /** @var array $products */ ?>

<div class="wrap">
    <h1><?php _e('Add-ons', 'affilicious'); ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="post-body-content">

                <div class="aff-addons-container">
                    <?php foreach ($products as $product): ?>
                        <div class="aff-addons-item">
                            <img class="aff-addons-item-image" src="<?php echo esc_attr($product['info']['thumbnail']); ?>" />

                            <div class="aff-addons-item-content">
                                <h3 class="aff-addons-item-title"><?php echo esc_html($product['info']['title']); ?></h3>
                                <p class="aff-addons-item-text"><?php echo esc_html($product['info']['excerpt']); ?></p>

                                <a class="aff-addons-item-link" href="<?php echo esc_attr($product['info']['link']); ?>" target="_blank">
                                    <?php _e('Discover now', 'affilicious'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="aff-addons-item aff-addons-item-soon-more">
                        <div class="aff-addons-item-content">
                            <img class="aff-addons-item-soon-more-icon" src="<?php echo AFFILICIOUS_ROOT_URL . 'assets/admin/dist/img/plus.svg'; ?>">
                            <p class="aff-addons-item-soon-more-text"><?php _e('More to come soon', 'affilicious'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2><span><?php _e('About Affilicious Theme', 'affilicious'); ?></span></h2>

                        <div class="inside">
                            <p><?php echo sprintf(__('We offer a collection of amazing affiliate plugins and themes for Wordpress. Visit our <a href="%s" target="_blank">official website</a> for more information.', 'affilicious'), 'http://docs.affilicioustheme.com'); ?></p>
                            <p>© Copyright 2016 - <?php echo date('Y'); ?></p>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2><span><?php _e('Resources &amp; Reference', 'affilicious'); ?></span></h2>

                        <div class="inside">
                            <ul>
                                <li>
                                    <a href="https://affilicioustheme.de/downloads/category/erweiterungen/" target="_blank"><?php _e('Add-ons', 'affilicious'); ?></a>
                                </li>
                                <li>
                                    <a href="https://affilicioustheme.de/downloads/category/themes/" target="_blank"><?php _e('Themes', 'affilicious'); ?></a>
                                </li>
                                <li>
                                    <a href="http://docs.affilicioustheme.de" target="_blank"><?php _e('Documentation', 'affilicious'); ?></a>
                                </li>
                                <li>
                                    <a href="https://affilicioustheme.de/support/" target="_blank"><?php _e('Support', 'affilicious'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br class="clear">
    </div>
</div>
