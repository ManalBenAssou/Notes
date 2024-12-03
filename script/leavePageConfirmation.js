$(document).ready(function() {
    // Variables pour stocker les valeurs actuelles du titre et du contenu
    var currentTitle = $(".note-title").val();
    var currentContent = $(".content").val();
    var noteId = $("#noteId").val();
    var encodedUrl = $("#encodedUrl").val();
    console.log(currentTitle);
    console.log(currentContent);

    // Gestionnaire d'événements pour le changement de titre
    $("#notetitle").on("input", function() {
        checkChanges();
    });

    // Gestionnaire d'événements pour le changement de contenu
    $(".content").on("input", function() {
        checkChanges();
    });

    // Fonction pour vérifier les changements
    function checkChanges() {
        var newTitle = $("#notetitle").val();
        var newContent = $(".content").val();

        return (newTitle !== currentTitle || newContent !== currentContent);
    }

    $("#back").click(function(event) {
        event.preventDefault(); // Empêche le comportement par défaut du lien
        if (checkChanges())
            $('#myModal').modal('show'); // Affiche la modalité
        else
            window.location.href = "note/openNote/"+noteId+"/"+encodedUrl;
    });

    // Gestionnaire d'événements pour le bouton "Leave Page"
    $("#leaveButton").click(function() {
        // Rediriger vers la page user/notes
        window.location.href = "note/openNote/"+noteId+"/"+encodedUrl;
    });
});