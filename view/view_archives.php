<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Notes</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/note_archive.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <div class="title-line">
                <div class="row">
                    <div class=" col-md-12 col-12">
                        <!-- Left side with title -->
                        <button class="btn " style="color: #ffffff" data-bs-toggle="offcanvas" data-bs-target="#sidebar" > <i class="fas fa-bars"></i></button>
                        <h4 class="float-end" style="color: #ffffff"> My archives</h4>
                    </div>
                </div>
            </div>
            <h5>Archives </h5>
            <?php 
                $archivesNote = array_filter($notes, function ($note) {
                    return $note->isArchived();
                });
            ?>
            <?php if (!empty($archivesNote)): ?>
                <div class="archived-notes row">    
                    <?php foreach ($archivesNote as $note): ?>
                        <?php
                            // Vérifier le type de la note
                            $noteType = ($note instanceof TextNote) ? 'TextNote' : (($note instanceof CheckListNote) ? 'ChecklistNote' : 'OtherNote');
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <a href="note/openNote/<?= $note->getId() ?>" class="note-link">                
                                <div class="note-row card" style="background-color: #181818;color :#ffffff">
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
                                    <div class="note-labels card-footer">
                                        <?php if (!empty($note->getLabels())): ?>
                                            <?php foreach ($note->getLabels() as $label): ?>
                                                <div class="label"><?php echo $label ?></div>                
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a> 
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h4 style="font-style: italic;">Your archives are empty.</h4>
            <?php endif; ?>
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
            </div>
        </div>      
        <!-- Bootstrap JS (include Popper for Bootstrap 4) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  
    </body>
</html>