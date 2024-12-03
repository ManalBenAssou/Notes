$(document).ready(function(){
    $(".pinned-notes, .other-notes").sortable({
        connectWith: ".pinned-notes, .other-notes", // Connect both sections for sorting
        //containment: "parent", // Restrict movement within the parent container
        stop: function(event, ui) {

            // Déterminer la section dans laquelle la note est déposée
            var dropzone = ui.item.parent().hasClass("pinned-notes") ? "pinned-notes" : "other-notes";
            console.log("Note déposée dans la zone : " + dropzone);

            // Array to store sorted note IDs
            var sortedIDs = [];

            // Récupérer les identifiants triés uniquement à partir de la section de dépôt actuelle
            ui.item.parent().find('.note-row').each(function() {
                // Get the note ID from data attribute
                var noteId = $(this).data('note-id');
                // Push the note ID to sortedIDs array
                sortedIDs.push(noteId);
            });
            console.log(sortedIDs);

            var dataToSend = {
                sortedIDs: sortedIDs,
                dropzone: dropzone 
            };

            $.ajax({
                type: "POST",
                url: "note/updateNoteWeights",
                data: dataToSend, 
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });

        }
    }).disableSelection();
    var moveDivs = document.getElementsByClassName('move-note');
    for (var i = 0; i < moveDivs.length; i++) {
        moveDivs[i].style.display = 'none';
    }
});
