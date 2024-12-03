$(document).ready(function(){
    // Stocker l'ordre d'origine des éléments
    var originalOrder = $('.content').map(function() {
        return $(this).data('item-id');
    }).get();

    $(".checkButton, .unCheckButton").click(function(event) {
        event.preventDefault();
        var item_id = $(this).data('item-id');

        $.ajax({
            url: "note/checkUncheckJs",
            type: "POST",
            data: { item_id: item_id },
            dataType: "json", // Expect JSON response
            success: function(response) {
                console.log("Success :", response);

                // Update the corresponding item on the page
                var checked = response.checked;
                var itemId = response.item_id;
                var itemElement = $('[data-item-id="' + itemId + '"]');
                var contentElement = itemElement.closest('.content');


                if (checked) {
                    // If checked, move the item to the end of the list
                    contentElement.detach().appendTo('.fields');
                    itemElement.find('.bi').removeClass('bi-square').addClass('bi-check-square');
                    itemElement.closest('.content').css('text-decoration', 'line-through');
                } else {
                    itemElement.find('.bi').removeClass('bi-check-square').addClass('bi-square');
                    itemElement.closest('.content').css('text-decoration', 'none');
                }
                // Sort the list based on checked status
                var $fields = $('.fields');
                var $uncheckedItems = $fields.find('.content').not('[style*="line-through"]').detach();
                var $checkedItems = $fields.find('.content[style*="line-through"]').detach();
                $fields.append($uncheckedItems).append($checkedItems);
            },
            error: function(xhr, status, error) {
                console.error("Error :", error);
            }
        });
    });
});
