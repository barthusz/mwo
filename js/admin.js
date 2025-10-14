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

    // Add intro image button
    var introImageUploader;
    $('.mwo-add-intro-image-button').on('click', function(e) {
        e.preventDefault();

        // Create the media uploader
        introImageUploader = wp.media({
            title: 'Kies achtergrondafbeeldingen',
            button: {
                text: 'Voeg toe'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });

        // When images are selected, add them to the list
        introImageUploader.on('select', function() {
            var attachments = introImageUploader.state().get('selection').toJSON();

            attachments.forEach(function(attachment) {
                var imageHtml = '<div class="mwo-intro-image-item" style="display: inline-block; margin: 5px; position: relative;">' +
                    '<img src="' + attachment.url + '" style="max-width: 150px; height: auto; display: block;">' +
                    '<button type="button" class="button mwo-remove-intro-image" data-image-id="' + attachment.id + '" style="position: absolute; top: 5px; right: 5px; padding: 2px 8px;">×</button>' +
                    '<input type="hidden" name="mwo_options[intro_images][]" value="' + attachment.id + '">' +
                    '</div>';

                $('.mwo-intro-images-list').append(imageHtml);
            });
        });

        // Open the media uploader
        introImageUploader.open();
    });

    // Remove intro image button
    $(document).on('click', '.mwo-remove-intro-image', function(e) {
        e.preventDefault();
        $(this).closest('.mwo-intro-image-item').remove();
    });
});
