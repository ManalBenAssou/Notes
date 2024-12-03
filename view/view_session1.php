<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>session1</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/shares_style.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {
                var checkedNotesIds = [];

                $(".notes-checkbox").on("change", function(event) {
                    event.preventDefault();
                    // Parcourez tous les éléments cochés
                    $(".notes-checkbox:checked").each(function() {
                        checkedNotesIds.push($(this).val()); // Ajoutez la valeur de l'élément cochée au tableau
                    });

                    console.log("checkedNotesIds: " + checkedNotesIds);
                    $("#toggleButton").prop('disabled', false);

                    
                });
                
                $("#toggleButton").click(function(event) {
                    $.ajax({
                        url: "note/toggeCheckItems",
                        type: "POST",
                        data: { checkedNotesIds: checkedNotesIds },
                        //dataType: "json",
                        success: function(response) {
                            console.log("reponse : " +response);
                            
                            
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", error);
                        }
                    });
                });
                
            });
        </script>  
    </head>
    <body>       
        <div class="main">

                <form method="POST" action="user/session1">
                        <div class="users-list">
                            <select id="user-options" name="user_id">
                                <option value="-1">-User-</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user->getId(); ?>"> <?= $user->getFullName(); ?> </option>
                                <?php endforeach; ?>
                                    
                            </select>
                        </div>
                        <button type="submit"id= "buttonSubmit" class="add-btn btn-primary">
                            Ok
                        </button>
                </form> 
            <div>
                <?php foreach($userChecklistNotes as $note): ?>
                    
                    <div class="notes">
                        <input type="checkbox" class="notes-checkbox" id="note" name="checkedNotes[]" value="<?= $note->getId(); ?>" >
                        <label for="note"><?= $note->getTitle() . " ( ".$note->getCheckedItemsCount() . " / ". count($note->getChecklistItems()) ." checked)" ?></label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary" id="toggleButton" disabled> 
                    Toggle
                </button>

            </div>             
                           
        </div>
    </body>
</html>
