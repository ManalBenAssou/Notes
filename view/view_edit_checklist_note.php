<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Edit checklist note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/edit_checklist_notes.css" rel="stylesheet" type="text/css"/>
        <script src = "lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/validation_title.js" type="text/javascript"></script> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="script/leavePageConfirmation.js" type="text/javascript"></script>
        <script src="script/newItemCheckNote.js" type="text/javascript"></script>
        <script src="script/itemsValidation.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="header">
            <input type="hidden" id="noteId" name="noteId" value="<?= $note->getId() ?>">
            <input type="hidden" id="encodedUrl" value="<?= $encodedUrl ?>">
            <?php if (isset($_GET['param2'])):?>
                <a href="note/openNote/<?= $note->getId()?>/<?= $_GET['param2'] ?>" id="back"><i class="fa fa-chevron-left"></i></a>
            <?php else:?>
                <a href="note/openNote/<?= $note->getId()?>" id="back"><i class="fa fa-chevron-left"></i></a>
            <?php endif ?>    
        </div>
        <div class="main">
            <form method="post" class="save-edit-form" action="note/editCheckListNote/<?= $note->getId()?>/<?=$encodedUrl?>">
                <div class="btn-wrapper">
                    <button type="submit" id="btnSave" class="btn btn-primary">
                        <i class="fas fa-save" title="Save"></i>
                    </button>
                </div>
                <div class="dates">
                    Created <?php echo $note->dateFormat($creationDate); ?> ago.
                    <?php if ($editionDate): ?>
                        Edited <?php echo $note->dateFormat($editionDate); ?>
                    <?php else: ?>
                        Not edited yet
                    <?php endif; ?>
                </div>
                <div class="input-fields">
                    <label class="title-label">Title</label>
                    <input type="text" id="notetitle" name="editedTitle" class="note-title" value="<?= isset($_POST['editedTitle']) ? $_POST['editedTitle'] : $note->getTitle() ?>">
                    <input type="hidden" id="titleMaxLength" value="<?= Configuration::get("title_max_length"); ?>">
                    <input type="hidden" id="titleMinLength" value="<?= Configuration::get("title_min_length"); ?>">
                    <input type="hidden" id="itemMaxLength" value="<?= Configuration::get("item_max_length"); ?>">
                    <input type="hidden" id="itemMinLength" value="<?= Configuration::get("item_min_length"); ?>">

                    <div id="errTitle" class="errors">
                        <?php if (!empty($titleErrors)): ?>
                            <ul>
                                <?php foreach ($titleErrors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <label class="items-label">Items</label>
                    <input type="hidden" id="itemsLength" value="<?=count($itemsList)?>">
                    <div class="items-list">
                        <?php foreach ($itemsList as $item): ?>
                            <div class="item-container">
                                <div class="input-container">
                                    <input type="checkbox" name="option1" <?php echo $item->isChecked() ? 'checked' : ''; ?> disabled>
                                    <input type="text" class="item-input" id="item<?= $item->getId() ?>" name="content<?= $item->getId() ?>" data-note-id="<?= $note->getId(); ?>" data-item-id="<?= $item->getId(); ?>" value="<?= isset($_POST['content'.$item->getId()]) ? $_POST['content'.$item->getId()] : $item->getContent() ?>">
                                    <input type="hidden" id="itemValue" name="itemValue" value="<?= $item->getContent() ?>">
                                </div>
                                <div class="delete-form">
                                    <button type="submit"  formaction="note/deleteItem/<?= $note->getId()?>/<?= $item->getId() ?>/<?=$encodedUrl?>"class="btn btn-primary" style="background-color: red;">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </div>                       
                            </div>
                            <div id="errItemTitle" class = "errors">
                                <?php if (!empty($itemsErrors[$item->getId()])): ?>                           
                                    <ul>
                                        <?php foreach ($itemsErrors[$item->getId()] as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>                       
                                <?php endif; ?> 
                            </div>   
                        <?php endforeach; ?>
                    </div>
                </div>
            </form> 
        
            <form method="post" action="note/addItem/<?= $note->getId()?>/<?= $encodedUrl?>" class="add-form">
                <label class="new-item-label">New Item</label>
                <div class="new-item-container">
                    <input type="text" name="content" id="new-item" class="new-item-input" data-note-id="<?=$note->getId();?>" value="<?= isset($_POST['content']) ? $_POST['content'] : '' ?>">
                    <button type="submit" class="btn btn-primary" style="background-color: blue;">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div id = "errNewItem" class = "errors">
                    <?php if (!empty($newItemErrors)): ?>
                        <ul>
                            <?php foreach ($newItemErrors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>              
            </form>
        </div>
        
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
    </body>
</html>
