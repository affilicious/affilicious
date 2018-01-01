<div class="notice notice-info <?php if(!empty($dismissible_id)): ?>is-dismissible<?php endif; ?>" <?php if(!empty($dismissible_id)): ?>data-dismissible-id="<?php echo esc_attr($dismissible_id); ?>"<?php endif; ?>>
	<p><?php echo $message; ?></p>
</div>
