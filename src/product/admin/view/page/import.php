<?php
    /** @var array $import_pages */
    /** @var array $current_import_page */
    /** @var array $admin_urls */
?>

<div class="wrap">
    <h1 style="margin-bottom: 10px;"><?php _e('Import', 'affilicious'); ?></h1>

    <div class="nav-tab-wrapper" style="margin-bottom: 30px;">
        <?php foreach ($import_pages as $import_page): ?>
            <a href="<?php echo esc_url($admin_urls[$import_page['slug']]); ?>"
               class="aff-import-page-tab aff-import-page-tab-<?php echo esc_attr($import_page['slug']) ?> nav-tab <?php if($admin_urls[$current_import_page['slug']] === $admin_urls[$import_page['slug']]): ?>nav-tab-active<?php endif; ?>">
                <?php echo esc_html($import_page['title']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php call_user_func($current_import_page['render']); ?>
</div>
