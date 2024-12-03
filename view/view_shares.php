<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shares</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/shares_style.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/share_note.js" type="text/javascript"></script>  
    </head>
    <body>       
        <div class="main">
            <div class="back-icon"><a href="note/openNote/<?=$note->getId()?>/<?=$dataUrl?>"><i class="fa fa-chevron-left"></i></a></div>
            <div class="title">Shares : </div>
            <div class="shares-list">
                <?php 
                    $noteId = $note->getId();
                    $usersSharedWith = $note->getUsersSharedWith();
                    if (!$isSharedNote):
                ?>
                    <p>This note is not shared yet</p>
                <?php else: ?>                   
                    <?php foreach ($usersSharedWith as $user): ?>
                        <div class="shared-users-container">
                            <div class="shared-users"><?= $user->getFullName() ?> <span style="font-style: italic;"><?= $note->isSharedWithEditPerrmission($user->getId()) ? '(editor)' : '(reader)' ?></span></div>
                            <div class="shared-users-buttons">
                                <form method="POST" id="toggle-permission-form" action="note/togglePermission/<?= $dataUrl?>">
                                    <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                    <input type="hidden" name="user_id" value="<?= $user->getId() ?>">
                                    <input type="hidden" name="permission" value="<?= $note->isSharedWithEditPerrmission($user->getId()) ? 'editor' : 'reader' ?>">
                                    <button type="submit" class="toggle-btn btn-primary" data-note-id="<?= $noteId ?>" data-user-id="<?= $user->getId() ?>" data-permission="<?= $note->isSharedWithEditPerrmission($user->getId()) ? 'editor' : 'reader' ?>">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                </form>
                                <form method="POST" id="delete-share-form" action="note/deleteShare/<?= $dataUrl?>">
                                    <input type="hidden" name="note_id" value="<?= $noteId ?>">
                                    <input type="hidden" name="user_id" value="<?= $user->getId() ?>">
                                    <input type="hidden" name="permission" value="<?= $note->isSharedWithEditPerrmission($user->getId()) ? 'editor' : 'reader' ?>">
                                    <button type="submit" class="delete-btn btn-primary" data-note-id="<?= $noteId ?>" data-user-id="<?= $user->getId() ?>" data-permission="<?= $note->isSharedWithEditPerrmission($user->getId()) ? 'editor' : 'reader' ?>" style="background-color: red;">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" id="add-share-form" action="note/addShare/<?= $dataUrl?>">
                    <input type="hidden" name="note_id" id ="noteId" value="<?= $noteId ?>">                        
                    <?php 
                        $showUserList = false;
                        foreach ($users as $user) {
                            $isShared = false;
                            foreach ($usersSharedWith as $sharedUser) {
                                if ($user->get_user_idFromDatabase() == $sharedUser->get_user_idFromDatabase()) {
                                    $isShared = true;
                                    break;
                                }
                            }
                            $isOwner = ($user->get_user_idFromDatabase() == $note->getOwnerId());
                            if (!$isShared && !$isOwner) {
                                $showUserList = true;
                                break;
                            }
                        }
                    ?>
                    <?php if ($showUserList): ?>
                        <div class="users-list">
                            <select id="user-options" name="user_id">
                                <option value="-1">-User-</option>
                                <?php 
                                    foreach ($users as $user) {
                                        $isShared = false;
                                        foreach ($usersSharedWith as $sharedUser) {
                                            if ($user->get_user_idFromDatabase() == $sharedUser->get_user_idFromDatabase()) {
                                                $isShared = true;
                                                break;
                                            }
                                        }
                                        $isOwner = ($user->get_user_idFromDatabase() == $note->getOwnerId());
                                        if (!$isShared && !$isOwner) {
                                            echo '<option value="' . $user->get_user_idFromDatabase() . '">' . $user->getFullName() . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="permission-list">
                            <select id="permission-options" name="permission">
                                <option value="option1">-Permission-</option>
                                <option value="reader">reader</option>
                                <option value="editor">editor</option>
                            </select>
                        </div>
                        <button type="submit"id= "buttonSubmit" class="add-btn btn-primary">
                            <i class="bi bi-plus"></i>
                        </button>
                    <?php endif; ?>
                </form>                
            </div>               
        </div>
    </body>
</html>
