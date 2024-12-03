<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add text note</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="css/add_text_notee.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
        <script src="script/validation_title.js" type="text/javascript"></script>
        <script src="script/validationContent.js" type="text/javascript"></script>
    </head>
    <body>
        <form action="note/addTextNote" method="post">
            <div class="main">
                <a href="user/notes"><i class="fa fa-chevron-left" style="margin-top: 30px; margin-left: 30px;"></i></a>
                <div class="btn-wrapper">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" title="Save"></i>
                    </button>           
                </div>
                <div class="input-fields">
                    <label class="title-label" for="notetitle">Title</label>
                    <input type="text" id="notetitle" name="notetitle" value="<?php echo isset($_POST['notetitle']) ? $_POST['notetitle'] : '' ?>">
                    <input type="hidden" id="titleMaxLength" value="<?= Configuration::get("title_max_length"); ?>">
                    <input type="hidden" id="titleMinLength" value="<?= Configuration::get("title_min_length"); ?>">
                    <input type="hidden" id="contentMaxLength" value="<?= Configuration::get("content_max_lenght"); ?>">

                    <div class="errors" id = "errTitle">
                        <?php if (isset($errors) && count($errors) != 0): ?>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                
                    <label class="title-label"  for="noteContent">Text</label>
                    <textarea type="text" id="noteContent" name="noteContent" <?php echo isset($_POST['noteContent']) ? $_POST['noteContent'] : '' ?> ></textarea>
                    <div class="errors" id = "errContent">
                        <?php if (isset($errorContent) && count($errorContent) != 0): ?>
                            <ul>
                                <?php foreach ($errorContent as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>
