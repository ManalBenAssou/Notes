$(function(){
    $('.save-edit-form').submit(function(e) {
        $noteId = $("#noteId").val();
        $encodedUrl = $("#encodedUrl").val();   
        e.preventDefault();
        var formData = $(this).serialize(); // Récupère les données du formulaire
        $.ajax({
            type: "POST",
            url: "note/editChecklistNote/"+$noteId+"/"+$encodedUrl,
            data: formData,
            success: function() {  
                console.log("success");
                window.location.href = "note/openNote/"+$noteId+"/"+$encodedUrl;   
            },
            error: function(xhr,satus,error){
                console.log(error);
            }              
        });
    });
    
    title = $("#notetitle");
    errTitle = $("#errTitle");
    titleMaxLength = $("#titleMaxLength").val();
    titleMinLength = $("#titleMinLength").val();

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

    let originalTitle = title.val().trim(); // Stockez le titre original lors du chargement de la page
    async function validateTitle() {
        let ok = true;
        errTitle.html("");
        title.removeClass('is-invalid is-valid');
        
        if (title.val().trim().length < titleMinLength || title.val().trim().length > titleMaxLength) {
            errTitle.append("<p>Title length must be between 3 and 25.</p>");
            ok = false;
        }

        if (title.val().trim() !== originalTitle) { // Vérifiez si le titre a changé
            const isUnique = await checkTitleExist();
            ok = ok && isUnique;
        }

        updateInputStyle(ok, title);
        enableSubmitButton(ok);
        return ok;
    }

    async function checkTitleExist(){
        let isUnique = true; // On suppose que le titre est unique par défaut
        try {
            const data = await $.post("note/note_exists_service", {"title" : title.val()});
            if (data === "true") {
                isUnique = false; 
            }
        } catch (error) {
            console.error("Error checking title uniqueness", error);
        }
    
        if (!isUnique) {
            errTitle.append("<p>Title must be unique</p>");
        }
        return isUnique;
    }  

    title.bind("input", validateTitle);

    $("input:text:first").focus();
});
