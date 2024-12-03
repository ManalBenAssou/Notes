$(function(){
    deleteForm();
    label = $("#labelName");
    errLabel = $("#errNewLabel");
    noteId = $("#noteId").val();

    // Vérifiez le nombre de labels au chargement initial
    if ($('.labels-container .label').length === 0) {
        $('.text').text('This note does not yet have a label');
    } else {
        $('.text').text('');
    }

    $('.add-label-form').submit(function(e){
        noteId = $("#noteId").val();
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type : "POST",
            url: "note/addLabelJs",
            data : formData,
            dataType: "json",
            success : function(response){
                updateView(response);
            },
            error: function(xhr,status,error){
                console.error("Error:", error);
            }
        })
    });

    function deleteForm(){
        $('.deleteForm').submit(function(e){
            noteId = $("#noteId").val();
            e.preventDefault();
            var formData = $(this).serialize();
            const button = $(e.currentTarget);
            $.ajax({
                type : "POST",
                url: "note/deleteLabel",
                data : formData,
                success : function(){
                    button.closest('.label').remove();
                    // Vérifiez le nombre de labels restants
                    if ($('.labels-container .label').length === 0) {
                        $('.text').text('This note does not yet have a label');
                    }
                },
                error: function(xhr,status,error){
                    console.error("Error:", error);
                }
            })
        });
    }

    function updateView(response){
        let html = '';
        html += `
        <div class="label">
            <form method="POST" class="deleteForm" action="note/deleteLabel">
                <div class="label-content"> ${response.label} </div>
                <input type="hidden" name="noteId" value="${noteId}">
                <input type="hidden" name="label" value=" ${response.label}">           
                <button type="submit" class="delete-btn btn-primary">
                    <i class="bi bi-dash"></i>
                </button>   
            </form>
        </div>
        `;
        if ($(".labels-list").val() == 0) {
            $('.text').text('');
            $('.labels-container').append(html);
        } else {
            $('.label:first').before(html); // Utilisez jQuery pour mettre à jour le HTML
        }
        $('.add-input').val('');
        $('.add-input').css({
            'border': '1px solid white',   // Removes the border
            'outline': 'none'   // Ensures no outline is left
        });
        deleteForm();
    }

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

    let originalLabel = label.val().trim(); // Stockez le titre original lors du chargement de la page
    async function validateLabel() {
        let ok = true;
        errLabel.html("");
        label.removeClass('is-invalid is-valid');
        
        if(!(/^.{2,10}$/).test(label.val().trim())){
            errLabel.append("<p>Label length must be between 2 and 10.</p>");
            ok = false;
        }

        if (label.val().trim() !== originalLabel) { // Vérifiez si le titre a changé
            const isUnique = await checkLabelExist();
            ok = ok && isUnique;
        }

        updateInputStyle(ok, label);
        enableSubmitButton(ok);
        return ok;
    }

    async function checkLabelExist(){
        let isUnique = true; // On suppose que le titre est unique par défaut
        try {
            const data = await $.post("note/labelExistsService", {"label" : label.val(), "noteId" : noteId});
            if (data === "true") {
                isUnique = false; 
            }
        } catch (error) {
            console.error("Error checking label uniqueness", error);
        }
    
        if (!isUnique) {
            errLabel.append("<p>A note cannot contain the same label twice</p>");
        }
        return isUnique;
    }  

    label.bind("input", validateLabel);

    $("input:text:first").focus();
});
