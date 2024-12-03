function updateInputStyle(isValid, element) {
    if (isValid) {
        element.css({
            'border': '2px solid green',
            'outline': 'none'
        });
        //errorElement.html(""); // Clear error message
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

//let errNewItem;
$(document).ready(function() {
    // Initialisez les valeurs initiales pour chaque élément
    $('.new-item-input').each(function() {
        errNewItem = $('#errNewItem');
    });

    // Attachez un gestionnaire d'événements au changement de contenu de chaque champ d'élément
    $('.new-item-input').on('input', function() {
        validateNewItem($(this)); // Appel de la fonction validateNewItem avec le champ d'élément en tant que paramètre
    });
    $('.delete-form button').on('click', handleDeleteButtonClick);
    

    $('.add-form').submit(function(e) {
        noteId = $("#noteId").val();
        encodedUrl = $("#encodedUrl").val();   
        e.preventDefault();
        var formData = $(this).serialize(); // Récupère les données du formulaire
        $.ajax({
            type: "POST",
            url: "note/addItemJs/"+noteId+"/"+encodedUrl,
            data: formData,
            dataType: "json",
            success: function(response) {
                console.log("success");
                updateView(response);
            },
            error: function(xhr,satus,error){
                console.log(error);
            }              
        });
    });
    function updateView(item) {
        console.log(item);
        let html = '';
        html += `
            <div class="item-container">
                <div class="input-container">
                    <input type="checkbox" name="option1" disabled ${item.checked ? 'checked' : ''}>
                    <input type="text" class="item-input" id="${item.id}" name="content${item.id} data-note-id="${item.noteId}" data-item-id="${item.id}" value="${item.content}">
                </div>
                <div class="delete-form">
                    <button type="submit"  formaction="note/deleteItem/${item.noteId}/${item.id}/${item.$encodedUrl}"class="btn btn-primary" style="background-color: red;">
                        <i class="bi bi-dash"></i>
                    </button>
                </div>  
            </div>

            `;
        if($("#itemsLength").val()==0){
            $('.items-list').append(html);
        }
        else{
            $('.item-container:first').before(html); // Utilisez jQuery pour mettre à jour le HTML
        }
        $('.new-item-input').val('');
        $('.new-item-input').css({
            'border': '1px solid white',   // Removes the border
            'outline': 'none'   // Ensures no outline is left
        });
        $('.delete-form button').off('click').on('click', handleDeleteButtonClick);
        attachValidationEvents($(`#${item.id}`));
    }
    function handleDeleteButtonClick(e) {
        e.preventDefault();
        const button = $(e.currentTarget);
        const action = button.attr('formaction');
        $.ajax({
            type: "POST",
            url: action,
            success: function(response) {
                // Suppression de l'élément de la vue après succès
                button.closest('.item-container').remove();
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
    function attachValidationEvents(itemInput) {
        itemInput.on('input', function() {
            validateItem($(this));
        });
    }  
});
async function validateItem(itemInput) {
    let ok = true;   
    const noteId = $("#noteId").val();
    console.log("noteId "+noteId);

    errItem = $('#errItemTitle');
    //const noteId = itemInput.data('note-id');
    const itemId = itemInput.data('item-id');
    const itemValue = itemInput.val().trim();
    errItem.html("");
    itemInput.removeClass('is-invalid is-valid');
    itemMaxLength = $("#itemMaxLength").val();
    itemMinLength = $("#itemMinLength").val();
    
    if(itemValue.length < itemMinLength || itemValue.length > itemMaxLength){
        errNewItem.append("<p>Item length must be between 1 and 60.</p>");
        ok = false;
    }

    const originalItemValue = originalItems[itemId];
    if (itemValue !== originalItemValue) { // Vérifiez si la valeur de l'élément a changé
        const isUnique = await checkNewItemExist(itemValue, noteId);
        ok = ok && isUnique;
    }

    updateInputStyle(ok, itemInput);
    enableSubmitButton(ok);
    return ok;
}

async function validateNewItem(itemInput) {
    let ok = true;
    //const noteId = itemInput.data('note-id');;
    noteId = $("#noteId").val();
    const itemValue = itemInput.val().trim();
    const errNewItem = $("#errNewItem");

    itemMaxLength = $("#itemMaxLength").val();
    itemMinLength = $("#itemMinLength").val();

    errNewItem.html("");
    itemInput.removeClass('is-invalid is-valid');
    
    if(itemValue.length < itemMinLength || itemValue.length > itemMaxLength){
        errNewItem.append("<p>Item length must be between 1 and 60.</p>");
        ok = false;
    }
    const isUnique = await checkNewItemExist(itemValue, noteId);
    ok = ok && isUnique;

    updateInputStyle(ok, itemInput);
    enableSubmitButton(ok);
    return ok;
}


async function checkNewItemExist(itemValue, noteId) {
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
        errNewItem.append("<p>Items must be unique</p>");
    }

    return isUnique;
}
