jQuery(document).ready(function ($) {
    // Handler per il selettore a discesa
    $('#quantity-selector').on('change', function () {
        updateVariantSelectors(parseInt($(this).val()));
    });

    // Handler per i pulsanti radio
    $('input[type=radio][name=quantity]').on('change', function () {
        updateVariantSelectors(parseInt($(this).val()));
    });

    function updateVariantSelectors(selectedQuantity) {

        if (isNaN(selectedQuantity)) {
            // Gestisci il caso in cui selectedQuantity non sia un numero
            return;
        }

        let selectorsContainer = $('#variant-selectors-container');
        let currentSelectors = selectorsContainer.find('.individual-variant-selector').length;

        // Aggiungi selettori di varianti se necessario
        for (let i = currentSelectors + 1; i <= selectedQuantity; i++) {
            let clonedSelector = selectorsContainer.find('.individual-variant-selector:first').clone();
            clonedSelector.show();
            selectorsContainer.append(clonedSelector);
        }

        // Rimuovi selettori di varianti in eccesso
        while (currentSelectors > selectedQuantity) {
            selectorsContainer.find('.individual-variant-selector:last').remove();
            currentSelectors--;
        }
    }
});