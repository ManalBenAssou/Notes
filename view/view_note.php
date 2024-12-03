<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Notes</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/notes_style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <?php
                // Vérifier le type de la note
                $noteType = ($note instanceof TextNote) ? 'TextNote' : (($note instanceof CheckListNote) ? 'ChecklistNote' : 'OtherNote');
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="note/openNote/<?= $note->getId() ?>/<?= $encodedUrl ?>" class="note-link row">                
                    <div class="note-row card">
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
                            <?php endif; ?>
                        </div> 
                        <div class="note-labels">
                            <?php if (!empty($note->getLabels())): ?>
                                <?php foreach ($note->getLabels() as $label): ?>
                                    <div class="label"><?php echo $label ?></div>                                           
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>                                                
                    </div>
                </a>
            </div>
        </div>
    </body>
</html>
