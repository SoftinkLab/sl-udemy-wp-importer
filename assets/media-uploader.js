jQuery(document).ready(function ($) {
    var mediaUploader;
    $('#upload_image_button').click(function (e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Video',
            library: {
                type: 'video'
            },
            button: {
                text: 'Choose Video'
            }, multiple: false
        });
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#background_image').val(attachment.title);
            $('#slui_video_id').val(attachment.id);
        });
        mediaUploader.open();
    });
});