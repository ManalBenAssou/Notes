<!DOCTYPE html>
<html>
    <head>
        <title>Edit Profile</title>
        <meta charset="UTF-8">
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/edit_profile.css" rel="stylesheet" type="text/css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container">
            <h2 class="title">Edit Profile</h2>
            <div style="position: absolute; top: 0; left: 0; padding: 10px;">
                <a href="user/settings"> <i class="fa fa-chevron-left" style="color: white;"></i> </a>
            </div>
            <form action="user/edit_profile" method="post">
                <div class="form-group">
                    <div class="input-wrapper">
                        <input name="username" id="username" type="text" placeholder="Enter your new username" value="<?= $newUsername ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input name="email" id="email" type="email" placeholder="Enter your new email" value="<?= $newEmail ?>">
                    </div>
                </div>
                <input type="submit" value="Save Changes">
            </form>
            <?php if (count($errors) != 0): ?>
                <div class="errors">
                    <p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
