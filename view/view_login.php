<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign In</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/login_style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>        
        <div class="main">
            <div class="title">Sign in</div>
            <form action="main/login" method="post">
                <table>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-person" style="font-size:larger;"></i></td>
                        <td><input id="mail" name="mail" type="email" placeholder="Enter your e-mail address" value="<?= isset($mail) ? $mail : '' ?>"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-key" style="font-size:larger;"></i></td>
                        <td><input id="password" name="password" type="password" placeholder="Enter your password" value="<?= isset($password) ? $password : '' ?>"></td>
                    </tr>
                </table>
                <div class="login">
                    <input type="submit" value="Login">
                </div>
            </form>
            <div class="menu">
                <a href="main/signup">New here? Click here to subscribe!</a>
            </div>
            <?php if (isset($errors) && count($errors) != 0): ?>
                <div class='errors'>
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
