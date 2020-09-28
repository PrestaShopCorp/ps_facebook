$(document).ready(function () {
    prestashop.on('updateProduct', function (params) {
        if (params.eventType === 'updatedProductCombination') {
            var productId = $('input[name="id_product"]').val();
            var $productAttributes = $(params.event.handleObj.selector);
            var selectedAttribute = $(params.event.originalEvent.srcElement);
            var attributes = [];
            $productAttributes.each(function (key, attribute) {
                if ($(attribute).is("input") && !$(attribute).is(':checked')) {
                    return;
                }
                attributes.push($(attribute).val());
            })
            ajaxProductCombinationChange(productId, attributes);
        }
    });


    function ajaxProductCombinationChange(productId, attributes) {
        $.ajax({
            type: 'POST',
            url: ajaxController,
            data: {
                action: 'CustomizeProduct',
                ajax: true,
                id_product: productId,
                attribute_ids: attributes,
            }
        });
    }
});
