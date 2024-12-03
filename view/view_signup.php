<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="css/signupp.css" rel="stylesheet" type="text/css"/>    
    </head>
    <body>
        <div class="main">
            <div class="title">Sign Up</div>
            <form id="signupForm" action="main/signup" method="post">
                <table>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-envelope" style="font-size:larger;"></i></td>
                        <td><input id="mail" name="mail" type="email" placeholder="Email" value="<?= $mail ?>"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-person" style="font-size:larger;"></i></td>
                        <td><input id="full_name" name="full_name" type="text" placeholder="Full Name" value="<?= $full_name ?>"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-key" style="font-size:larger;"></i></td>
                        <td><input id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>"></td>
                    </tr>
                    <tr>
                        <td class="icon-cell"><i class="bi bi-key" style="font-size:larger;"></i></td>
                        <td><input id="password_confirm" name="password_confirm" type="password" placeholder="Confirm your password" value="<?= $password_confirm ?>"></td>
                    </tr>
                </table>
                <input type="submit" value="Sign Up" >               
                <a href = "main/login"><input type="button" value="Cancel" ></a>   
            </form>
            <?php if (count($errors) != 0): ?>
                <div class='errors'>
                    <br><br><p>Please correct the following error(s) :</p>
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