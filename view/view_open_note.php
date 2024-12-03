<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <base href="<?= $web_root ?>"/>
        <title>Barre d'icônes</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">   
        <link href="css/open_notes.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="script/deleteNote.js" type="text/javascript"></script>
    </head>
    <body>
        <!-- fenêtre modale de condirmation pour supprimer une note -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-dialog-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Are you sure ?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Do you really want to delete note " <?php echo $note->getTitle()?>" and all of its dependencies ?
                        <br></br>
                        This process cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="deleteButton" class="btn btn-primary" data-note-id="<?= $note->getId(); ?>">Yes, delete it !</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de la fenêtre modale -->

        <!-- fenêtre modale pour le message de succession apres la suppression de la note -->
        <div class="modal fade" id="nouvelleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-dialog-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Deleted</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        This note has been deleted.                     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de la fenêtre modale -->

        <div class="icon-bar">
            <?php
                $noteType = $note->getNoteType();
                $noteId = $note ->getId();
            ?>
            <?php if ($noteType == 'archived' ): ?>
                <div class="menu">
                    <a href="user/archives"><i class="fa fa-chevron-left"></i></a>
                    <a href="note/showMessageConfirm/<?=$noteId?>" id="link"><i class="bi bi-trash3"></i></a>
                    <form method="POST" action="note/unarchive/<?=$noteId?>/<?= $_GET['param2'] ?>">
                        <button type="submit" class="buttons"><i class="bi bi-archive-fill"></i></button>
                    </form>                  
                </div>
                
            <?php elseif ($noteType == 'normal' || $note->getOwnerId() === $user->getId()): ?>
                <div class="menu">
                    <?php if (isset($_GET['param2'])):?>
                        <a href="note/searchNotes/<?= $_GET['param2'] ?>"><i class="fa fa-chevron-left"></i></a>
                    <?php else:?>
                        <a href="user/notes"><i class="fa fa-chevron-left"></i></a>
                    <?php endif ?>
                    <a href="note/shareNote/<?=$noteId?>/<?= $_GET['param2'] ?>"><i class="bi bi-share"></i></a>
                    <?php if ($note->isPinned()): ?>
                        <form method="POST" action="note/unPin/<?=$noteId?> /<?= $_GET['param2'] ?>">
                            <button type="submit" class="buttons"><i class="bi bi-pin-fill"></i></button>
                        </form>
                    <?php else:?>
                        <form method="POST" action="note/pin/<?=$noteId?>/<?= $_GET['param2'] ?>">
                            <button type="submit" class="buttons"><i class="bi bi-pin"></i></button>
                        </form>
                    <?php endif ?>
                    <form method="POST" action="note/labels/<?=$noteId?>/<?= $_GET['param2'] ?>">
                        <button type="submit" class="buttons"><i class="bi bi-tag"></i></i></button>
                    </form> 
                    <form method="POST" action="note/archive/<?=$noteId?>/<?= $_GET['param2'] ?>">
                        <button type="submit" class="buttons"><i class="bi bi-archive"></i></button>
                    </form> 
                    <?php if (isset($_GET['param2'])):?>
                        <a href="note/editNote/<?=$noteId?>/<?= $_GET['param2'] ?>"><i class="bi bi-pencil"></i></a>
                    <?php else:?>
                        <a href="note/editNote/<?=$noteId?>"><i class="bi bi-pencil"></i></a>
                    <?php endif ?>
                </div>
            
            <?php elseif ($noteType == 'shared'):?>
                <div class="menu">
                    <?php if (isset($_GET['param2'])):?>
                        <a href="note/searchNotes/<?= $_GET['param2'] ?>"><i class="fa fa-chevron-left"></i></a>
                    <?php else:?>
                        <a href="user/sharedNotes/<?= $note->getOwnerId() ?>"><i class="fa fa-chevron-left"></i></a>
                    <?php endif ?>
                    <?php if ($note->isSharedWithEditPerrmission($user->getId())):?>
                        <form method="POST" action="note/labels/<?=$noteId?>/<?= $_GET['param2'] ?>">
                            <button type="submit" class="buttons"><i class="bi bi-tag"></i></i></button>
                        </form>
                        <?php if (isset($_GET['param2'])):?>
                            <a href="note/editNote/<?=$noteId?>/<?= $_GET['param2'] ?>"><i class="bi bi-pencil"></i></a>
                        <?php else:?>
                            <a href="note/editNote/<?=$noteId?>"><i class="bi bi-pencil"></i></a>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            <?php else:?>
            <?php endif ?>
        </div>
    </body>
</html>