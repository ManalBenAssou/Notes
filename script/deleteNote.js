$(document).ready(function(){
    $("#link").click(function(event){
        event.preventDefault(); // Empêche le comportement par défaut du lien
        $('#myModal').modal('show'); // Affiche la modalité
    });


    $("#deleteButton").click(function(){
        // Récupérer l'identifiant de la note à supprimer
        var noteId = $(this).data('note-id');
        console.log(noteId);
        
        // Envoyer une requête AJAX pour supprimer la note
        $.ajax({
            url: "note/deleteNoteJs",
            type: "POST",
            data: { noteId: noteId },
            success: function(response) {
                // Traiter la réponse de la requête AJAX
                console.log("Note deleted successfully");
                // Fermer la première fenêtre modale
                $('#myModal').modal('hide');                            
                $('#nouvelleModal').modal('show');
            },
            error: function(xhr, status, error) {
                // Gérer les erreurs de la requête AJAX
                console.error("Error deleting note:", error);
            }
        });
    });

    // Écoutez l'événement hidden.bs.modal sur la deuxième fenêtre modale
    $('#nouvelleModal').on('hidden.bs.modal', function () {
        // Actualiser la page
        location.reload();
    });
});