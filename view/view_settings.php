<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Settings</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/settings_styless.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        
        <div class="main">
            <div class="title-line"><a href="user/notes"> &lt; </a>  Settings</div>
            <div class="greet">Hey <span> <?= $user->getFullName() ?></span>!</div>
            <?php include('menu.html'); ?>
                
        </div>
    </body>
</html>
