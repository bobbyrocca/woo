jQuery(document).ready(function ($) {
    console.log("Documento pronto.");

    $('#quantity-selector, #variant-selector').on('change', function () {
        console.log("Evento change rilevato.");

        let quantity = $('#quantity-selector').val();
        let product_id = adrocket_ajax_object.product_id;
        let variation_id = $('#variant-selector').val() || '';

        console.log("Quantità selezionata:", quantity);
        console.log("ID Prodotto:", product_id);
        console.log("ID Variante:", variation_id);
        console.log("URL AJAX:", adrocket_ajax_object.ajax_url);

        $.ajax({
            url: adrocket_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_updated_price',
                product_id: product_id,
                quantity: quantity,
                variation_id: variation_id
            },
            success: function (response) {

                console.log("Risposta ricevuta:");

                let json_response = JSON.parse(response);

                if (json_response) {

                    console.log({response: json_response});
                    // Aggiorna il prezzo di vendita
                    if (json_response.sale_price) {
                        console.log("Prezzo di vendita:", json_response.sale_price);
                        $('.wp-block-woocommerce-product-price .sale-price .woocommerce-Price-amount bdi').html(json_response.sale_price);
                    }

                    // Aggiorna il prezzo regolare
                    if (json_response.regular_price) {
                        console.log("Prezzo regolare:", json_response.regular_price);
                        $('.wp-block-woocommerce-product-price .regular-price .woocommerce-Price-amount bdi').html(json_response.regular_price);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", xhr, status, error);
            }
        });
    });
});
