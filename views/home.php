<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Home</title>
        <link rel="stylesheet" type="text/css" href="assets/home.css">
    </head>
    <body>
        <div class="firstline"></div>
        <header>
            <div class="title"><span>F</span>iles<span>A</span>dministrator</div>
            <div class="inscription">
            <?php if (empty($_SESSION['id'])): ?>
                <a href="index.php?action=register">Inscription !</a>
            <?php else: ?>
            <p class="connected">Hi <?php echo $_SESSION['username'] ?> !<a class="deco" href="index.php?action=deco">Déconnexion</a></p></p><a href="index.php?action=files">Mes fichiers !</a>
            <?php endif; ?>
            </div>
        </header>
        <main>
            <div class="description">
                <p>Bienvenue sur <span>F</span>iles<span>A</span>dministrator !</p>
                <ul>
                    <li>Connectez vous !</li>
                    <li>Stockez vos fichiers !</li>
                    <li>Modifiez les !</li>
                    <li>Supprimez les !</li>
                </ul>
                <p></p>
            </div>
            <div class="formOne">
                <fieldset>
                    <p>Vous avez déja un compte ? Connectez vous !</p>
                  <form method="POST" action="index.php" >
                        <label for="username">Nom d'utilisateur : </label><input required type="text" name="username" id="username">
                        <label for="password">Mot de passe : </label><input required type="password" name="password" id="password">
                        <input type="submit" value="Connexion">
                    </form>
                        <?php if (!empty($error)): ?>
                        <p><?php echo $error ?></p>
                        <?php endif; ?>
                </fieldset>
            </div>              
        </main>
        <div class="footerline"></div>
        <footer>
            <div><span>F</span>iles<span>A</span>dministrator by a random dev</div>
        </footer>
    </body>
</html>