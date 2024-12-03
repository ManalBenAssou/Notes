<!DOCTYPE html>
<html>
    <head>
        <title>Change Password</title>
        <meta charset="UTF-8">
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/change_password.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>   
        <div class="main">
            <div style="position: absolute; top: 0; left: 0; padding: 10px;">
                <a href="user/settings"> <i class="fa fa-chevron-left" style="color: white;"></i> </a>
            </div>
            <div class="title">Change Password</div>
            <form action="user/change_password" method="post">
                <table>
                    <tr>
                        <td class="icon-cell"><i class="glyphicon glyphicon-lock white-icon"></i></td>
                        <td><input name="currentPassword" id="currentPassword" type="password" placeholder="Current Password"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="glyphicon glyphicon-lock white-icon"></i></td>
                        <td><input name="newPassword" id="newPassword" type="password" placeholder="New Password"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="glyphicon glyphicon-lock white-icon"></i></td>
                        <td><input name="confirmPassword" id="confirmPassword" type="password" placeholder="Confirm New Password"></td>
                    </tr>
                </table>
                <div class="login">
                    <input type="submit" value="Change Password">
                </div>
            </form>
            <?php if (isset($errors) && count($errors) != 0): ?>
                <div class='errors'>
                    <p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif(count($errors) != 0): ?>
                <div class="sucess">
                    <p> Your password has been successfully updated :</p>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
