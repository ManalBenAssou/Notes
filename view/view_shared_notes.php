<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Shared Notes</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">       
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/notes_style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <div class="title-line">
                <div class="row">
                    <div class=" col-md-12 col-12">
                        <!-- Left side with title -->
                        <button class="btn " style="color: #ffffff" data-bs-toggle="offcanvas" data-bs-target="#sidebar" > <i class="fas fa-bars"></i></button>
                        <h4 class="float-end" style="color: #ffffff">Shared by <?= $sharedUser->getFullName(); ?></h4>
                    </div>
                </div>
            </div>
            <?php if (!empty($notesWithEditPermission)): ?>
                <h5>Notes shared to you by <?= $sharedUser->getFullName(); ?> as editor</h5>               
                <div class="shared-notes">
                    <?php foreach ($notesWithEditPermission as $note): ?>
                        <?php include("view_note.php") ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($notesWithoutEditPermission)): ?>
                <h5>Notes shared to you by <?= $sharedUser->getFullName(); ?> as reader </h5>
                <div class="shared-notes">
                    <?php foreach ($notesWithoutEditPermission as $note): ?>
                        <?php include("view_note.php") ?>
                    <?php endforeach; ?>
                </div>
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