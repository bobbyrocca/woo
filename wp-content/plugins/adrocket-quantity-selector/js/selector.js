jQuery(document).ready(function ($) {
    // Handler for dropdown
    $('#quantity-selector').on('change', function () {
        updateVariantSelectors($(this));
    });

    // Handler for radio buttons
    $('#quantity-selector-radio input[type=radio]').on('click', function () {
        updateVariantSelectors($(this));
    });

    function updateVariantSelectors(element) {
        let selectedQuantity = parseInt(element.val());
        let selectorsContainer = $('#variant-selectors-container');
        let currentSelectors = selectorsContainer.find('.individual-variant-selector').length;

        // Add variant selectors if needed
        for (let i = currentSelectors + 1; i <= selectedQuantity; i++) {
            let clonedSelector = selectorsContainer.find('.individual-variant-selector:first').clone();
            clonedSelector.show();
            selectorsContainer.append(clonedSelector);
        }

        // Remove excess variant selectors
        while (currentSelectors > selectedQuantity) {
            selectorsContainer.find('.individual-variant-selector:last').remove();
            currentSelectors--;
        }
    }
});
