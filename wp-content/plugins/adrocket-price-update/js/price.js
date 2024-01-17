jQuery(document).ready(function ($) {
    console.log("Documento pronto.");

    $(document).on('change', '#quantity-selector', function () {
        let quantity = parseInt($('#quantity-selector').val());
        updatePrice(quantity);
    });

    $(document).on('change', 'input[type=radio][name=quantity], #variant-selectors-container input[type=radio]', function () {
        let quantity = parseInt($('input[type=radio][name=quantity]:checked').val());
        updatePrice(quantity);
    });

    function updatePrice(quantity) {

        if (isNaN(quantity)) {
            // Gestisci il caso in cui selectedQuantity non sia un numero
            return;
        }

        let product_id = adrocket_ajax_object.product_id;
        let variation_ids = [];
        /*
                $('#variant-selectors-container .individual-variant-selector:visible').each(function () {
                    variation_ids.push($(this).val());
                });*/

        // Raccogli gli ID delle varianti dai pulsanti radio selezionati
        $('#variant-selectors-container input[type=radio]:checked').each(function () {
            variation_ids.push($(this).val());
        });

        console.log("Quantità selezionata:", quantity);
        console.log("ID Prodotto:", product_id);
        console.log("ID Varianti:", variation_ids);

        $.ajax({
            url: adrocket_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_updated_price',
                product_id: product_id,
                quantity: quantity,
                variation_ids: variation_ids // Ora è un array di ID
            },
            success: function (response) {

                console.log("Risposta ricevuta:");

                let json_response = JSON.parse(response);

                if (json_response) {

                    console.log({response: json_response});
                    // Aggiorna il prezzo di vendita
                    if (json_response.sale_price && json_response.hasOwnProperty('wp_sale_price')) {
                        console.log("Prezzo di vendita:", json_response.sale_price);
                        $('.wp-block-woocommerce-product-price .sale-price .woocommerce-Price-amount bdi').html(json_response.wp_sale_price);
                    }

                    // Aggiorna il prezzo regolare
                    if (json_response.regular_price && json_response.hasOwnProperty('wp_regular_price')) {
                        console.log("Prezzo regolare:", json_response.regular_price);
                        $('.wp-block-woocommerce-product-price .regular-price .woocommerce-Price-amount bdi').html(json_response.wp_regular_price);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", xhr, status, error);
            }
        });
    }
});
