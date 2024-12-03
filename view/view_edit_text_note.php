<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Edit Text Note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/edit_text_note.css" rel="stylesheet" type="text/css"/>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/validation_title.js" type="text/javascript"></script>  
        <script src="script/validationContent.js" type="text/javascript"></script>  
        <script src="script/leavePageConfirmation.js" type="text/javascript"></script>
    </head>
    <body>
        <!-- fenêtre modale de confirmation -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-dialog-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Unsaved changes !</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to leave this form ?
                    <br></br>
                        Changes you made will not be saved.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="leaveButton" class="btn leave-btn">Leave Page</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de la fenêtre modale -->
        <header>
            <input type="hidden" id="noteId" name="noteId" value="<?= $note->getId() ?>">
            <input type="hidden" id="encodedUrl" value="<?= $encodedUrl ?>">
            <div class = "menu">
                <?php if (isset($_GET['param2'])):?>
                    <a href="note/openNote/<?= $note->getId()?>/<?= $_GET['param2'] ?>" id="back"><i class="fa fa-chevron-left"></i></a>
                <?php else:?>
                    <a href="note/openNote/<?= $note->getId()?>" id="back"><i class="fa fa-chevron-left"></i></a>
                <?php endif ?>
            </div> 
        </header>
        <form method="post" action="note/editTextNote/<?= $note->getId()?>/<?=$encodedUrl?>">
            <div class="btn-wrapper">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save" title="Save"></i>
                </button>          
            </div>
            <div class="main">
                <div class="dates">
                    Created <?php echo $note->dateFormat($creationDate); ?> ago.
                    <?php if ($editionDate): ?>
                        Edited <?php echo $note->dateFormat($editionDate); ?>
                    <?php else: ?>
                        Not edited yet
                    <?php endif; ?>
                </div>
                <div class="input-fields">
                    <label class="title-label" for="notetitle">Title</label>
                    <input type="text" id="notetitle"  name ="editedTitle" class ="note-title" value ="<?php echo isset($_POST['editedTitle']) ? $_POST['editedTitle'] : $note->getTitle() ?>">
                    <input type="hidden" id="titleMaxLength" value="<?= Configuration::get("title_max_length"); ?>">
                    <input type="hidden" id="titleMinLength" value="<?= Configuration::get("title_min_length"); ?>">
                    <input type="hidden" id="contentMaxLength" value="<?= Configuration::get("content_max_lenght"); ?>">
        
                    <div class='errors' id = "errTitle">
                        <?php if (isset($errors) && count($errors) != 0): ?>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <label class="title-label">Text</label>
                    <textarea type="text" id="noteContent" name="editedContent" class="content"><?php echo isset($_POST['editedContent']) ? $_POST['editedContent'] : $note->getContent() ?></textarea>
                    <div class="errors" id = "errContent"></div>
                </div> 
            </form>
        </div>
    </body>
</html>
