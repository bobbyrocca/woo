jQuery(document).ready(function ($) {
    console.log("Documento pronto.");

    $(document).on('change', '#quantity-selector', function () {
        console.log("Cambio valore selettore quantità")
        let quantity = parseInt($('#quantity-selector').val());
        updatePrice(quantity);
    });

    $(document).on('change', 'input[type=radio][name=quantity], #variant-selectors-container input[type=radio]', function () {
        console.log("Cambio valore pulsante radio (price.js)")
        let quantity = parseInt($('input[type=radio][name=quantity]:checked').val());

        if (isNaN(quantity)) {
            quantity = parseInt($('#quantity-selector').val());
        }

        updatePrice(quantity);
    });

    $(document).on('click', '#adrocket-add-to-cart', function () {

        console.log("Cliccato pulsante Aggiungi al carrello")

        let quantity = parseInt($('input[type=radio][name=quantity]:checked').val());

        if (isNaN(quantity)) {
            quantity = parseInt($('#quantity-selector').val());
        }

        adrocket_add_to_cart(quantity);
    });

    function updatePrice(quantity) {

        if (isNaN(quantity)) {
            // Gestisci il caso in cui selectedQuantity non sia un numero
            console.log("Quantità non valida")
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
                        $('#sales-price').html(json_response.wp_sale_price);

                        // Aggiorna il prezzo regolare
                        if (json_response.regular_price && json_response.hasOwnProperty('wp_regular_price')) {

                            if (json_response.regular_price > json_response.sale_price) {
                                console.log("Prezzo regolare:", json_response.regular_price);
                                $('#regular-price').html(json_response.wp_regular_price);
                                $('#discount-row').removeClass('hide');
                                $('#discount_percentage').html(json_response.discount_percentage + '%');
                            } else {
                                $('#regular-price').html('');
                                $('#discount-row').addClass('hide');
                            }
                        }
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore AJAX:", xhr, status, error);
            }
        });
    }

    function adrocket_add_to_cart(quantity) {

        if (isNaN(quantity)) {
            // Gestisci il caso in cui selectedQuantity non sia un numero
            console.log("Quantità non valida")
            return;
        }

        console.log("quantità:", quantity)

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

        // Invia i dati al server tramite AJAX
        $.ajax({
            url: adrocket_ajax_object.ajax_url,
            type: 'POST',
            data: {
                'action': 'adrocket_add_to_cart',
                'product_id': product_id,
                'quantity': quantity,
                'variation_ids': variation_ids
            },
            success: function (response) {
                console.log("Risposta ricevuta:");

                let json_response = JSON.parse(response);

                if (json_response) {

                    console.log({response: json_response});
                    if(json_response.hasOwnProperty('cart_url')) {
                        window.location.href = json_response.cart_url;
                    }else{
                        window.location.href = '/cart/';
                    }
                }
            },
            error: function (error) {
                console.error('Errore AJAX:', error);
            }
        });
    }
});
