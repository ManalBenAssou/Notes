$(document).ready(function() {
    console.log("-- Début --");
    update();
    
});
function update(){
    $('#add-share-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize(); // Récupère les données du formulaire
        $.ajax({
            type: "POST",
            url: "note/addShare",
            data: formData,
            success: function(response) {
                console.log("Partage ajouté avec succès");
                var shareList = $(response).find('.shares-list');
                $(".shares-list").replaceWith(shareList);
                update();

                

            },
            error: function(xhr,satus,error){
                console.log("Erreur lors de l'ajout de partage");
            }
            
        });
    });
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        console.log("Suppression de partage");

        var note_id = $(this).data('note-id');
        var user_id = $(this).data('user-id');
        var permission = $(this).data('permission');

        var dataToSend = {
            note_id: note_id,
            user_id: user_id,
            permission: permission 
        };

        $.ajax({
            type: "POST",
            url: "note/deleteShare",
            data: dataToSend,
            success: function(response) {
                console.log("success :");
                var shareList = $('.shares-list').empty();
                var shareList = $(response).find('.shares-list');
                $(".shares-list").replaceWith(shareList);
                update();


            },
            error: function(xhr,satus,error){
                console.log("Erreur lors de la suppression de partage");
            }
            
        });
    });
    $('.toggle-btn').click(function(e) {
        e.preventDefault();
        console.log("Modification des permissions");
        
        var note_id = $(this).data('note-id');
        var user_id = $(this).data('user-id');
        var permission = $(this).data('permission');

        var dataToSend = {
            note_id: note_id,
            user_id: user_id,
            permission: permission 
        };

        $.ajax({
            type: "POST",
            url: "note/togglePermission",
            data: dataToSend,
            success: function(response) {
                console.log("Permissions modifiées avec succès");
                var shareList = $(response).find('.shares-list');
                $(".shares-list").replaceWith(shareList);
                update();

            },
            error: function(xhr,satus,error){
                console.log("Erreur lors de la modification des permissions");
            }
            
        });
    });

}
/*$(document).ready(function() {
    console.log("-- Début --");
    update();
    updateEventListeners();
});

function update() {
    $('#add-share-form').submit(function(e) {
        e.preventDefault();
        soumettreFormulaireAjax($(this), "note/addShareJS");
    });

    $('#delete-share-form').submit(function(e) {
        e.preventDefault();
        soumettreFormulaireAjax($(this), "note/deleteShareJS");
    });

    $('#toggle-permission-form').submit(function(e) {
        e.preventDefault();
        soumettreFormulaireAjax($(this), "note/togglePermissionJS");
    });
}

function soumettreFormulaireAjax($form, url) {
    console.log(url);
    var formData = $form.serialize();
    console.log("formData " , formData);
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Erreur:', response.error);
            } else {
                // Mise à jour des partages et de la liste des utilisateurs disponibles
                mettreAJourListePartages(response.shares);
                mettreAJourListeUtilisateurs(response.availableUsers);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
        }
    });
    
}

function mettreAJourListePartages(partages) {
    console.log(partages);
    let htmlListePartages = '';
    if (partages.length === 0) {
        htmlListePartages = '<p>This note is not shared yet.</p>';
    } else {
        partages.forEach(partage => {
            htmlListePartages += construireHtmlPartage(partage);
        });
    }
    // Met à jour uniquement la partie de la liste de partages
    $('.shared-users-container').html(htmlListePartages);
    updateEventListeners();
}



function mettreAJourListeUtilisateurs(utilisateurs) {
    console.log(utilisateurs);
    let html = '';
    let htmlListeUtilisateurs = '<option value="-1">-User-</option>';
    utilisateurs.forEach(utilisateur => {
        htmlListeUtilisateurs += `<option value="${utilisateur.id}">${utilisateur.name}</option>`;
    });
    $('#user-options').html(htmlListeUtilisateurs);

    // Mettre à jour  la liste des permissions
    let htmlListePermissions = `
        <option value="option1">-Permission-</option>
        <option value="reader">reader</option>
        <option value="editor">editor</option>
    `;
    $('#permission-options').html(htmlListePermissions);
}



function construireHtmlPartage(partage) {
    console.log("construire");
    console.log(partage);
    console.log("fin construire");
    return `<div class="shared-users-container">
                <div class="shared-users">${partage.name} <span style="font-style: italic;">(${partage.permission})</span></div>
                <div class="shared-users-buttons">
                    <form method="POST" id="toggle-permission-form" action="note/togglePermissionJS">
                        <input type="hidden" name="note_id" value="${partage.noteId}">
                        <input type="hidden" name="user_id" value="${partage.userId}">
                        <input type="hidden" name="permission" value="${partage.permission}">
                        <button class="toggle-btn btn-primary" >
                        <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </form>
                    <form method="POST" id="delete-share-form" action="note/deleteShareJS">
                        <input type="hidden" name="note_id" value="${partage.noteId}">
                        <input type="hidden" name="user_id" value="${partage.userId}">
                        <input type="hidden" name="permission" value="${partage.permission}">
                        <button class="delete-btn btn-primary" style="background-color: red;">
                        <i class="bi bi-dash"></i>
                        </button>
                    </form>
                </div>
            </div>`;
}
/*function submitTogglePermission(button) {
    var $form = $(button).closest('form');
    soumettreFormulaireAjax($form, "note/togglePermissionJS");
}

function submitDeleteShare(button) {
    var $form = $(button).closest('form');
    soumettreFormulaireAjax($form, "note/deleteShareJS");
}*/
/*function updateEventListeners() {
    // Délégation pour le bouton de modification des permissions
    $(document).on('click', '.toggle-btn', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        soumettreFormulaireAjax($form, "note/togglePermissionJS");
    });

    // Délégation pour le bouton de suppression de partage
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        soumettreFormulaireAjax($form, "note/deleteShareJS");
    });
};*/



