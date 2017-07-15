<div class="aff-amazon-import">
    <div class="aff-amazon-import-search">
        <form class="aff-amazon-import-search-form" data-provider-configured="<?php if(!empty($amazon_provider_configured)): ?>true<?php else: ?>false<?php endif; ?>"></form>

        <?php include (__DIR__ . '/amazon/provider-not-configured-error.php'); ?>

        <div class="aff-amazon-import-search-results"></div>

        <div class="aff-amazon-import-load-more"></div>
    </div>

    <form class="aff-amazon-import-config"></form>
</div>

<?php include (__DIR__ . '/amazon/search-form-template.php'); ?>

<?php include (__DIR__ . '/amazon/search-results-item-template.php'); ?>

<?php include (__DIR__ . '/amazon/search-load-more-template.php'); ?>

<?php include (__DIR__ . '/amazon/config-template.php'); ?>
