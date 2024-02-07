jQuery(document).ready(function ($) {
    $(document).on('click', '.custom-add-to-cart-1', function () {
        console.log("Cliccato pulsante Aggiungi al carrello");

        // Trova il genitore più vicino con classe 'product-item'
        let $productItem = $(this).closest('.product-item');

        // Crea il div 'blocker' con un div 'spinner' interno
        let blockerDiv = '<div class="blocker"><div class="spinner"></div></div>';

        // Aggiunge il 'blockerDiv' al 'productItem'
        $productItem.append(blockerDiv);
    });

    $(document.body).on('removed_from_cart', function (event, fragments, cart_hash, $button) {
        if ($button) {
            // Ottiene l'ID del prodotto rimosso dal data attribute del pulsante
            var productId = $button.data('product_id');
            console.log("Rimosso dal carrello: Product ID - " + productId);
        }
    });

    // Ascolta l'evento 'updated_wc_div'
    $(document.body).on('updated_wc_div', function () {
        // Ricarica la pagina
        window.location.reload();
        console.log("Ricaricata la pagina")
    });

    // Ascolta l'evento 'updated_cart_totals'

    $(document.body).on('updated_cart_totals', function () {
        // Ricarica la pagina
        console.log("Aggiornati i totali del carrello")
    });

    // Ascolta l'evento click sui link di rimozione nel carrello
    $(document).on('click', '.woocommerce-cart-form .product-remove > a', function () {
        // Ottieni l'ID del prodotto
        var productId = $(this).data('product_id');
        console.log("Prodotto in fase di rimozione: Product ID - " + productId);

    });

    // wc_cart_emptied

    $(document.body).on('wc_cart_emptied', function () {

        console.log("Carrello svuotato")

    });

    $(document).on('change', '.woocommerce-cart-form .quantity .qty', function() {
        var $form = $(this).closest('form.woocommerce-cart-form');

        console.log("Modificata quantità")
        // Imposta un breve ritardo per consentire all'utente di completare le modifiche
        clearTimeout(window.update_cart_timeout);

            // Aggiorna il carrello inviando il form
            $form.find('button[name="update_cart"]').prop('disabled', false).trigger('click');

    });

});
