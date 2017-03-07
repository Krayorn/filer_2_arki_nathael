<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Incription</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="assets/register.css" rel="stylesheet">
    </head>
    <body>
        <div class="firstline"></div>
        <header>
            <div class="title"><span>F</span>iles<span>A</span>dministrator</div>
            <div>
                <a href="index.php?action=home">Retourner au menu principal !</a>
            </div>
        </header>
        <div class="center">
            <div class="form">
                <fieldset>
                    <legend>Inscription</legend>
                        <form method="POST" action="?action=register">
                            <label for="username">Nom d'utilisateur : </label><input required type="text" name="username" id="username"><br>
                            <label for="password">Mot de passe : </label><input required type="password" name="password" id="password"><br>
                            <label for="verifpassword">Confirmation du mot de passe : </label><input required type="password" name="verifpassword" id="verifpassword"><br>
                            <label for="email">E-mail : </label><input required type="email" name="email" id="email"><br>
                            <input type="submit" value="S'inscrire">
                        </form>
                        <?php if (!empty($error)): ?>
                        <p><?php echo $error ?></p>
                        <?php endif; ?>
                </fieldset>   
            </div>
        </div>
        <div class="footerline"></div>
        <footer>
            <div><span>F</span>iles<span>A</span>dministrator by a random dev</div>
        </footer>
    </body>
</html>