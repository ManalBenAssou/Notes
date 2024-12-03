<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Open Checklist Note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/open_checklist_note.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/checkUncheck.js" type="text/javascript"></script>
    </head>
    <body>
        <header>
            <?php include('view_open_note.php'); ?>    
        </header>
        <div class="main">
            <div class="dates">
                Created <?php echo $note->dateFormat($creationDate); ?> ago.
                <?php if ($editionDate): ?>
                    Edited <?php echo $note->dateFormat($editionDate); ?>
                <?php else: ?>
                    Not edited yet
                <?php endif; ?>
            </div>
            <div class="fields">
                <label class="title-label">Title</label>
                <div class="title"><?php echo $note->getTitle() ?></div>
                <label class="content-label">Items</label>
                <?php foreach ($items as $item): ?>
                    <?php if (!$item->isChecked()): ?>
                        <div class="content">
                            <form method="POST" action="note/checkUncheck">
                                <input type="hidden" name="encodedUrl" value="<?=$encodedUrl?>">
                                <input type="hidden" name="item_id" value="<?php echo $item->getId(); ?>">
                                <button type="submit" class="checkButton" data-item-id="<?= $item->getId(); ?>" >
                                    <i class="bi bi-square"></i>
                                </button>
                                <span class="content-text"><?php echo $item->getContent() ?></span>                            
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach ($items as $item): ?>
                    <?php if ($item->isChecked()): ?>
                        <div class="content" style="text-decoration: line-through;">
                            <form method="POST" action="note/checkUncheck">
                                <input type="hidden" name="encodedUrl" value="<?=$encodedUrl?>">
                                <input type="hidden" name="item_id" value="<?php echo $item->getId(); ?>">
                                <button type="submit" class="unCheckButton" data-item-id="<?= $item->getId(); ?>">
                                    <i class="bi bi-check-square" ></i>
                                </button>
                                <span class="content-text"><?php echo $item->getContent() ?></span>                            
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div> 
        </div>
    </body>
</html>
