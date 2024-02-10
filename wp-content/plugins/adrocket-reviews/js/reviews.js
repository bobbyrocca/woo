jQuery(document).ready(function($) {
    $('#upload-btn').on('click', function(e) {

        console.log('Upload button clicked')

        var file_data = $('#review-image').prop('files')[0];
        var form_data = new FormData();
        form_data.append('review-image', file_data);
        form_data.append('action', 'upload_review_image');
        form_data.append('comment_id', 'ID del commento qui'); // Assicurati di passare l'ID del commento correttamente

        $.ajax({
            url: adrocketReviewsData.ajaxurl, // Utilizza l'URL AJAX passato da PHP
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {
                alert('Immagine caricata con successo');
            },
            error: function(response) {
                alert('Errore nel caricamento');
            }
        });
    });
});
