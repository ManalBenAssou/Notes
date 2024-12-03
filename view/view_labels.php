<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Labels </title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/labels_style.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">   
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/newLabelValidation.js" type="text/javascript"></script>
    </head>
    <body>
        <div class ="main">
            <div class="back-icon"><a href="note/openNote/<?=$noteId?>/<?= $dataUrl?>"> 
                <i class="fa fa-chevron-left"></i> </a>
            </div>
            <div class="title">Labels : </div>
            <div class="labels-list">
                <div class="labels-container">
                    <?php if(empty($noteLabels)) : ?>
                        <div class="text">
                            <p style="margin-bottom: 20px;">This note does not yet have a label</p>
                        </div>
                    <?php else : ?>
                        <?php foreach($noteLabels as $noteLabel):?>
                            <div class="label">
                                <form method="POST" class="deleteForm" action="note/deleteLabel/<?= $dataUrl?>">
                                    <div class="label-content"> <?php echo $noteLabel?> </div>
                                    <input type="hidden" name="noteId" value="<?= $noteId ?>">
                                    <input type="hidden" name="label" value="<?= $noteLabel ?>">
                                    <button type="submit" class="delete-btn btn-primary">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif ?>
                </div>            
                <form method="POST" class ="add-label-form" action="note/addLabel/<?= $dataUrl?>">
                    <label for="label">Add new label:</label><br>
                    <input list="labels-list" id="labelName" class="add-input" name="label" value="<?php echo isset($_POST['label']) ? $_POST['label'] : '' ?>" placeholder="Type to search or create ...">
                    <datalist id="labels-list">
                        <?php foreach($labelsList as $label): ?>
                            <option value="<?= $label ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                    <input type="hidden" id="noteId" name="noteId" value="<?= $noteId ?>">
                    <button type="submit" id= "buttonSubmit" class="add-btn btn-primary">
                            <i class="bi bi-plus"></i>
                    </button>
                    <div id = "errNewLabel" class = "errors">
                        <?php if (!empty($errors)): ?>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>