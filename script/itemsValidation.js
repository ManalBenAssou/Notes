function updateInputStyle(isValid, element) {
    if (isValid) {
        element.css({
            'border': '2px solid green',
            'outline': 'none'
        });
    } else {
        element.css({
            'border': '2px solid red',
            'outline': 'none'
        });
    }
}

function enableSubmitButton(enable) {
    $("button[type=submit]").prop('disabled', !enable);
}
let originalItems = {}; // Créez un objet pour stocker les valeurs initiales de chaque élément

//let errItem;
$(document).ready(function() {
    // Initialisez les valeurs initiales pour chaque élément
    $('.item-input').each(function() {
        const itemId = $(this).data('item-id');
        originalItems[itemId] = $(this).val().trim();
        errItem = $('#errItemTitle');
    });

    // Attachez un gestionnaire d'événements au changement de contenu de chaque champ d'élément
    $('.item-input').on('input', function() {
        validateItem($(this)); // Appel de la fonction validateItem avec le champ d'élément en tant que paramètre
        

    });
});

async function validateItem(itemInput) {
    let ok = true;
    const noteId = $("#noteId").val();
    const itemId = itemInput.data('item-id');
    const itemValue = itemInput.val().trim();
    itemMaxLength = $("#itemMaxLength").val();
    itemMinLength = $("#itemMinLength").val();

    errItem.html("");
    itemInput.removeClass('is-invalid is-valid');
    
    if(itemValue.length < itemMinLength || itemValue.length > itemMaxLength){
        errItem.append("<p>Item length must be between 1 and 60.</p>");
        ok = false;
    }

    const originalItemValue = originalItems[itemId];
    if (itemValue !== originalItemValue) { // Vérifiez si la valeur de l'élément a changé
        const isUnique = await checkItemExist(itemValue, noteId);
        ok = ok && isUnique;
    }

    updateInputStyle(ok, itemInput);
    enableSubmitButton(ok);
    return ok;
}


async function checkItemExist(itemValue, noteId) {
    let isUnique = true;
    try {
        const data = await $.post("note/item_exists_service", {
            "itemValue": itemValue,
            "noteId": noteId
        });
        if (data === "true") {
            isUnique = false;
        }

    } catch (error) {
        console.error("Error checking item uniqueness", error);
    }

    if (!isUnique) {
        errItem.append("<p>Items must be unique</p>");
    }

    return isUnique;
}