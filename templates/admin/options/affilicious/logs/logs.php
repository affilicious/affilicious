<?php
/**
 * @var string $logs The logs.
 * @var string $download_url The URL to the logs download.
 */
?>

<div class="aff-logs">
    <a class="aff-logs-download button-primary" href="<?php echo esc_url($download_url); ?>" download><?php _e('Download Logs', 'affilicious'); ?></a>
    <p class="aff-logs-text"><?php echo $logs; ?></p>
</div>
