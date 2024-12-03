<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add checklist note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">   

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/add_checklist_note.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <form method="post" action="note/addCheckListNote">
            <div class="main">
                <a href="user/notes"><i class="fa fa-chevron-left" style="margin-top: 30px; margin-left: 30px;"></i></a>
                <div class="btn-wrapper">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" title="Save"></i>
                    </button>
                </div>
                <div class="input-fields">
                    <label class="title-label">Title</label>
                    <input type="text" id="notetitle" name="notetitle" value="<?php echo isset($_POST['notetitle']) ? $_POST['notetitle'] : '' ?>">
                    <?php if (!empty($errors['title'])): ?>
                        <div class='errors'>
                            <ul>
                                <?php foreach ($errors['title'] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <label class="items-label">Items</label>
                    <ul class="items">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <li>
                                <input type="text" class="item-input" id="item<?= $i ?>" name="item<?= $i ?>" value="<?php echo isset($_POST['item' . $i]) ? htmlspecialchars($_POST['item' . $i]) : '' ?>">
                                <?php if (!empty($errors['item'][$i])): ?>
                                    <div class='errors'>
                                        <ul>
                                            <?php foreach ($errors['item'][$i] as $error): ?>
                                                <li><?= $error ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </form>
    </body>
</html>
