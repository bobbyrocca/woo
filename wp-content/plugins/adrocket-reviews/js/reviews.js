jQuery(document).ready(function ($) {

    // Inizialmente disabilita il pulsante di upload
    $('#upload-btn').prop('disabled', true);

    // Abilita o disabilita il pulsante di upload in base alla selezione del file
    $('#review-image').on('change', function () {
        if ($(this).prop('files').length > 0) {
            $('#upload-btn').prop('disabled', false);
        } else {
            $('#upload-btn').prop('disabled', true);
        }
    });

    $("#review-link").click(function() {
        $("#review_form_wrapper").slideToggle();
    });

    $('#upload-btn').on('click', function (e) {
        e.preventDefault();

        console.log('Upload button clicked');

        var file_data = $('#review-image').prop('files')[0];
        var form_data = new FormData();
        form_data.append('review-image', file_data);
        form_data.append('action', 'upload_review_image');
        form_data.append('comment_id', adrocketReviewsData.commentID);
        var nonce = $('[name="custom_review_image_nonce"]').val();
        form_data.append('custom_review_image_nonce', nonce);

        $.ajax({
            url: adrocketReviewsData.ajaxurl, // Utilizza l'URL AJAX passato da PHP
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
                alert('Immagine caricata con successo');
                $('#upload-btn').prop('disabled', true); // Disabilita il pulsante dopo l'upload
                $('#review-image').val(''); // Resetta il campo di input del file
                console.log(response)
                if (response.success && response.data.hasOwnProperty('image_url')) {
                    console.log(response.data.image_url)
                    $('#review-image-display').attr('src', response.data.image_url);
                }
            },
            error: function (response) {
                alert('Errore nel caricamento');
            }
        });
    });
});
