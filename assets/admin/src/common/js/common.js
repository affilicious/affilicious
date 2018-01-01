jQuery(function($) {
    $(document).on('click', '.notice[data-dismissible-id] .notice-dismiss', function() {
        let dismissibleId = $(this).closest('.notice').data('dismissible-id');
        let url = `${ajaxurl}?dismissible-id=${dismissibleId}`;

        $.ajax(url, {
            type: 'POST',
            data: {
                'action': 'aff_dismissed_notice',
            }
        });
    });
});
