jQuery(document).ready(function ($) {

    let selected = []; // Salva le selezioni correnti
    let variant_sel = $('#variant-selectors-container');

    let originalVariantGroups = variant_sel.children('div').clone();

    // Se la quantità viene cambiata tramite radio buttons, aggiorna i pulsanti radio
    $('input[type=radio][name=quantity]').on('change', function () {
        console.log("Cambio valore pulsante radio (selector.js)")
        updateVariantSelectors(parseInt($(this).val()), variant_sel);
        updateRadioButtons();
    });

    // Se la quantità viene cambiata tramite il selettore, aggiorna i pulsanti radio

    $('#quantity-selector').on('change', function () {
        console.log("Cambio valore selettore quantità")
        updateVariantSelectors(parseInt($(this).val()), variant_sel);
        updateRadioButtons();
    });

    variant_sel.on('change', 'input[type=radio]', function () {
        updateRadioButtons();
    });

    updateRadioButtons();

    $(document).on('click', '#adrocket-add-to-cart', function () {
        // remove class hid to show the loader
        $('.blocker').removeClass('hide');
    });

    resetForm();

    $("#scroll-to-cart").click(function () {
        $('html, body').animate({
            scrollTop: $("#adrocket-block").offset().top - 20
        }, 1000); // 1000 millisecondi per l'animazione di scorrimento
    });

    $(window).scroll(function () {
        var top_of_element = $("#adrocket-add-to-cart").offset().top;
        var bottom_of_element = $("#adrocket-add-to-cart").offset().top + $("#adrocket-add-to-cart").outerHeight();
        var bottom_of_screen = $(window).scrollTop() + $(window).innerHeight();

        if (bottom_of_screen > bottom_of_element && $(window).scrollTop() > top_of_element) {
            // Lo scroll è sceso sotto adrocket-add-to-cart e adrocket-add-to-cart non è visibile
            $("#stick-single").removeClass('hide');
        } else {
            // adrocket-add-to-cart è visibile o lo scroll non ha ancora superato l'elemento
            $("#stick-single").addClass('hide');
        }
    });


    function updateVariantSelectors(selectedQuantity, selectorsContainer) {
        if (isNaN(selectedQuantity)) {
            // Gestisci il caso in cui selectedQuantity non sia un numero
            return;
        }

        // Salva le selezioni correnti prima di eliminarle
        selectorsContainer.find('.variant-radios').each(function (i) {
            $(this).find('input[type=radio]').each(function () {
                if ($(this).is(':checked')) {
                    selected[i] = $(this).val(); // Salva il valore del pulsante radio selezionato
                }
            });
        });

        selectorsContainer.empty(); // Svuota il container

        // Aggiungi gruppi di varianti per ogni quantità selezionata
        for (let i = 0; i < selectedQuantity; i++) {
            let clonedGroups = originalVariantGroups.clone(); // Clona i gruppi di varianti originali per questa quantità

            let productNumber = i + 1; // Numero progressivo del prodotto

            // Modifica solo il testo del numero all'interno di <div class="flex-1">
            clonedGroups.find('.product-index').text(productNumber);

            clonedGroups.find('input[type=radio]').each(function (index) {
                // Aggiorna gli attributi per ogni pulsante radio
                let newId = 'variant' + (i + 1) + '_' + index; // Crea un ID unico
                $(this).attr('id', newId);
                $(this).attr('name', 'variant[' + i + ']');
                $(this).next('label').attr('for', newId); // Aggiorna l'attributo 'for' del label

                // Seleziona lo stesso valore delle ultime varianti selezionate
                if (selected[i] && selected[i] === $(this).val()) {
                    $(this).prop('checked', true);
                }
            });
            selectorsContainer.append(clonedGroups); // Aggiunge i gruppi clonati al container
        }
    }

    function updateRadioButtons() {
        // Rimuovi la classe 'selected' da tutti i label
        $('#quantity-selector-radio label').removeClass('selected');

        // Trova il radio button selezionato e aggiungi la classe 'selected' al suo label parent
        $('#quantity-selector-radio input[type=radio]:checked').next().addClass('selected');

        // Rimuovi la classe 'selected' da tutti i label all'interno di variant-radios
        $('.variant-radios label').removeClass('selected');

        // Trova il radio button selezionato e aggiungi la classe 'selected' al suo label
        $('.variant-radios input[type=radio]:checked').next('label').addClass('selected');
    }

    function resetForm() {
        // Trova la prima opzione disponibile per la quantità e impostala
        let firstQtyOption = $('#quantity-selector option').first().val();
        $('#quantity-selector').val(firstQtyOption).change();

        // Seleziona la prima variante disponibile per ogni gruppo di varianti
        $('#variant-radios').each(function () {
            $(this).find('input[type=radio]').first().prop('checked', true);
        });

        // Aggiorna i pulsanti radio e altri elementi dell'interfaccia utente
        updateRadioButtons();
        updateVariantSelectors(parseInt(firstQtyOption), $('#variant-selectors-container'));
    }
});