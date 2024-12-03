<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Open Text Note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/open_text_note1.css" rel="stylesheet" type="text/css"/>
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
                <label class="content-label">Text</label>
                <div class="content"><?php echo $note->getContent() ?></div>
            </div> 
        </div>
    </body>
</html>
