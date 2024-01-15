jQuery(document).ready(function ($) {
    $('#quantity-selector').on('change', function () {
        var selectedQuantity = parseInt($(this).val());
        var selectorsContainer = $('#variant-selectors-container');
        var currentSelectors = selectorsContainer.find('.individual-variant-selector').length;

        // Aggiungi selettori di varianti se necessario
        for (var i = currentSelectors + 1; i <= selectedQuantity; i++) {
            var clonedSelector = selectorsContainer.find('.individual-variant-selector:first').clone();
            clonedSelector.show();
            selectorsContainer.append(clonedSelector);
        }

        // Rimuovi selettori di varianti in eccesso
        while (currentSelectors > selectedQuantity) {
            selectorsContainer.find('.individual-variant-selector:last').remove();
            currentSelectors--;
        }
    });
});