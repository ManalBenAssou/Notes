<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Notes</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/notes_style.css" rel="stylesheet" type="text/css"/>

        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="lib/jquery-ui.min.js" type="text/javascript"></script>
        <script src="lib/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
        <script src="script/drag&drop.js" type="text/javascript" defer></script>
    </head>
    <body>
        <div class="main">
            <div class="title-line">
                <div class="row">
                    <div class=" col-md-12 col-12">
                        <!-- Left side with title -->
                        <button class="btn " style="color: #ffffff" data-bs-toggle="offcanvas" data-bs-target="#sidebar" > <i class="fas fa-bars"></i></button>
                        <h4 class="float-end" style="color: #ffffff">My notes</h4>
                    </div>
                </div>
            </div>
            <?php
                $pinnedNotes = array_filter($notes, function ($note) {
                    return $note->isPinned() && !$note->isArchived();
                });
            ?>
            <?php if (!empty($pinnedNotes)): ?>
                <h5>Pinned </h5>
                <div class="pinned-notes row">
                    <?php foreach ($pinnedNotes as $note): ?>
                        <?php
                            // Vérifier le type de la note
                            $noteType = ($note instanceof TextNote) ? 'TextNote' : (($note instanceof CheckListNote) ? 'ChecklistNote' : 'OtherNote');
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3"  >
                            <a href="note/openNote/<?= $note->getId() ?>" class="note-link row">                
                                <div class="note-row card" draggable="true" data-note-id="<?= $note->getId(); ?>" data-weight="<?= $note->getWeight();?>">
                                    <div class="note-title card-header"><?= $note->getTitle() ?></div>
                                    <div class="note-content card-body">
                                        <?php if ($noteType === 'ChecklistNote'): ?>
                                            <ul style="list-style-type: none;">
                                                <?php foreach ($note->getChecklistItems() as $item): ?>
                                                    <li>                                               
                                                        <input type="checkbox" name="option1" value="valeur1" disabled <?php echo $item->isChecked() ? 'checked' : ''; ?>>
                                                        <?= $item->getContent(); ?>                                                
                                                    </li>                                
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php elseif ($noteType === 'TextNote'): ?>
                                            <div><?= $note->getTruncateContent() ?></div>
                                            
                                        <?php else: ?>
                                            <div><?= $note->content ?></div>
                                        <?php endif; ?>
                                    </div> 

                                    <div class="note-labels">
                                        <?php if (!empty($note->getLabels())): ?>
                                            <?php foreach ($note->getLabels() as $label): ?>
                                            <div class="label"><?php echo $label ?></div>
                                                
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                                    
                                    <div class="move-note card-footer " id="move">
                                        <?php 
                                            $directionLeft = "left";
                                            $directionRight = "right";
                                            $noteId = $note->getId();
                                        ?>
                                        <?php if ($note->getWeight() < $note->getMaxWeight()): ?>
                                            <form method="post" action="note/moveNote">
                                                <button type="submit" class="btn btn-primary move-note-left float-start" name="direction" value="<?= $directionLeft ?>" data-note-id="<?= $noteId ?>"><<</button>
                                                <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($note->getWeight() > $note->getMinWeight()): ?>
                                            <form method="post" action="note/moveNote">
                                                <button type="submit" class="btn btn-primary move-note-right float-end" name="direction" value="<?= $directionRight ?>" data-note-id="<?= $noteId ?>">>></button>
                                                <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>                
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php
                $otherNotes = array_filter($notes, function ($note) {
                    return !$note->isPinned() && !$note->isArchived();
                });
            ?>
        </div>
        <div class="main">
            <?php if (!empty($otherNotes)): ?>
                <h5>Others </h5>
                <div class="other-notes row">
                    <?php foreach ($otherNotes as $note): ?>
                        <?php
                            // Vérifier le type de la note
                            $noteType = ($note instanceof TextNote) ? 'TextNote' : (($note instanceof CheckListNote) ? 'ChecklistNote' : 'OtherNote');
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <a href="note/openNote/<?= $note->getId() ?>" class="note-link row">                
                                <div class="note-row card" draggable="true" data-note-id="<?= $note->getId(); ?>" data-weight="<?= $note->getWeight();?>">
                                    <div class="note-title card-header"><?= $note->getTitle() ?></div>
                                    <div class="note-content card-body">
                                        <?php if ($noteType === 'ChecklistNote'): ?>
                                            <ul style="list-style-type: none;">
                                                <?php foreach ($note->getChecklistItems() as $item): ?>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="option1" value="valeur1" disabled <?php echo $item->isChecked() ? 'checked' : ''; ?>>
                                                            <?= $item->getContent(); ?>
                                                        </label>
                                                    </li>                                
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php elseif ($noteType === 'TextNote'): ?>
                                            <div><?= $note->getTruncateContent() ?></div>
                                            
                                        <?php else: ?>
                                            <div><?= $note->content ?></div>
                                        <?php endif; ?>
                                    </div> 

                                    <div class="note-labels">
                                        <?php if (!empty($note->getLabels())): ?>
                                            <?php foreach ($note->getLabels() as $label): ?>
                                            <div class="label"><?php echo $label ?></div>
                                                
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                                    
                                    <div class="move-note card-footer" id="move2">
                                        <?php 
                                            $directionLeft = "left";
                                            $directionRight = "right";
                                            $noteId = $note->getId();
                                        ?>
                                        <?php if ($note->getWeight() < $note->getMaxWeight()): ?>
                                            <form method="post" action="note/moveNote">
                                                <button type="submit" class="btn btn-primary move-note-left" name="direction" value="<?= $directionLeft ?>" data-note-id="<?= $noteId ?>"><<</button>
                                                <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($note->getWeight() > $note->getMinWeight()): ?>
                                            <form method="post" action="note/moveNote">
                                                <button type="submit" class="btn btn-primary move-note-right" name="direction" value="<?= $directionRight ?>" data-note-id="<?= $noteId ?>">>></button>
                                                <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>              
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <!-- si l'utilisateur n'a aucune note -->
            <?php if (empty($pinnedNotes) && empty($otherNotes)): ?>
                <h4 style="font-style: italic;">Your notes are empty.</h4>
            <?php endif; ?>

            <div class="add-note-icons">
                <a href="note/addTextNote" class="add-text-note-icon" title="Add Text Note">
                    <i class="fas fa-file-alt"></i>
                </a>
                <a href="note/addCheckListNote" class="add-checklist-note-icon" title="Add Checklist Note">
                    <i class="fas fa-list"></i>
                </a>
            </div>
        </div>

        <!-- Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" >
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" style='color: #f7e702e2'>NoteApp</h5>
                <button type="button" class="btn-close btn-close-white"  data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div> 
            <div class="offcanvas-body">
                <a href="user/notes" class="d-block mb-2">My notes</a>
                <a href="note/search" class="d-block mb-2">Search</a>
                <a href="user/archives" class="d-block mb-2">My archives</a>
                <?php $sharedUsers = $user->getUsersSharedWithMe();?>
                <?php if ($sharedUsers !== null): ?>
                    <?php foreach ($sharedUsers as $sharedUser): ?>
                        <a href="user/sharedNotes/<?= $sharedUser->getId() ?>" class="d-block mb-2">Notes shared by <?php echo $sharedUser->getFullName();?></a>
                    <?php endforeach ?>
                <?php endif;?>             
                <a href="user/settings" class="d-block mb-2">Settings</a>
                <a href="user/session1" class="d-block mb-2">Session1</a>
            </div> 
        </div>
        <!-- Bootstrap JS (include Popper for Bootstrap 4) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
