<?php
/**
 * @var string $system_info The system info.
 * @var string $download_url The URL to the system info download.
 */
?>

<div class="aff-system-info">
    <a class="aff-system-info-download button-primary" href="<?php echo esc_url($download_url); ?>" download><?php _e('Download System Info', 'affilicious'); ?></a>
    <p class="aff-system-info-text"><?php echo $system_info; ?></p>
</div>
