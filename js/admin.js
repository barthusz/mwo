jQuery(document).ready(function($) {
    var mediaUploader;

    // Upload logo button
    $('.mwo-upload-logo-button').on('click', function(e) {
        e.preventDefault();

        // If the media uploader exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader
        mediaUploader = wp.media({
            title: 'Kies een logo',
            button: {
                text: 'Gebruik dit logo'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        // When an image is selected, update the preview and hidden field
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            $('#mwo-logo-id').val(attachment.id);

            // Update preview
            $('.mwo-logo-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; display: block;">');

            // Update button text
            $('.mwo-upload-logo-button').text('Wijzig logo');

            // Show remove button if not already visible
            if (!$('.mwo-remove-logo-button').length) {
                $('.mwo-upload-logo-button').after('<button type="button" class="button mwo-remove-logo-button">Verwijder logo</button>');
            }
        });

        // Open the media uploader
        mediaUploader.open();
    });

    // Remove logo button (using event delegation for dynamically added button)
    $(document).on('click', '.mwo-remove-logo-button', function(e) {
        e.preventDefault();

        $('#mwo-logo-id').val('');
        $('.mwo-logo-preview').html('');
        $('.mwo-upload-logo-button').text('Upload logo');
        $(this).remove();
    });
});
