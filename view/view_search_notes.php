<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Search</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/notes_style.css" rel="stylesheet" type="text/css"/>

        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/searchNotes.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="main">
            <div class="title-line">
                <div class="row">
                    <div class=" col-md-12 col-12">
                        <!-- Left side with title -->
                        <button class="btn " style="color: #ffffff" data-bs-toggle="offcanvas" data-bs-target="#sidebar" > <i class="fas fa-bars"></i></button>
                        <h4 class="float-end" style="color: #ffffff">Search my notes</h4>
                    </div>
                </div>
            </div>
            <p>Search notes by tags :</p>
            <form method="POST" action="note/search/<?= $encodedUrl ?>">
                <?php foreach($labels as $label): ?>
                    <?php
                        // Vérifiez si l'étiquette est cochée en fonction de la valeur soumise dans $_POST["checkedLabels"]
                        $checked = (isset($checkedLabels) && in_array($label, $checkedLabels)) ? "checked" : "";                       
                    ?>
                    <div class="labels">
                        <input type="checkbox" class="labels-checkbox" id="label" name="checkedLabels[]" value="<?= $label?>" <?= $checked?>>
                        <label for="label"><?= $label?></label>
                    </div>
                <?php endforeach;?>
                <br>
                <button type="submit" class="btn btn-primary" id="buttonSearch">Search</button>
            </form>
            <div class="my-notes">
                <?php if (!empty($myNotes)): ?>       
                    <h5>Your notes : </h5>
                    <?php foreach ($myNotes as $note): ?>
                        <?php include ("view_note.php") ?>            
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div id ="div-shared-note" class="main">
            <?php foreach ($user->getUsersSharedWithMe() as $sharedUser): 
                $sharedNotesByUser = $user->getNotesSharedWithMeByUser($sharedUser->getId()); 
                $sharedNotes = [];
                //$sharedNotes = array_intersect($sharedNotesByUser, $allSharedNotes);
                foreach($allSharedNotes as $note) {
                    foreach($sharedNotesByUser as $sharedNote) {
                        if($note->getId() == $sharedNote->getId()) {
                            if(!in_array($sharedNote, $sharedNotes)) {
                                $sharedNotes[] = $sharedNote;

                            }
                        }
                    }
                }               
            ?>  
            <?php if (!empty($sharedNotes)): ?>
                <h5 class="sharedUser">Notes shared by <?= $sharedUser->getFullName(); ?> </h5>
            <?php endif?>           
            <div class="shared-notes row">
                    <?php if (!empty($sharedNotes)): ?>
                        <?php foreach ($sharedNotes as $note): ?>
                            <?php include ("view_note.php") ?> 
                        <?php endforeach; ?>
                        
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
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
