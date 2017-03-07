<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Mes fichiers</title>
        <link rel="stylesheet" type="text/css" href="assets/files.css">
        <script src="assets/scripts.js"></script>
    </head>
    <body>
        <div class="firstline"></div>
        <header>
            <div class="title"><span>F</span>iles<span>A</span>dministrator</div>
            <p><?php echo $error ;?></p>
            <div class="inscription">
            <?php
                echo '<p class="connected">Hi ' . $_SESSION['username'] . ' !<a class="deco" href="?action=deco">Déconnexion</a></p>';
            ?>
            </div>
        </header>
        <div id="container">
            <div class="insertnewfiles">
                <form action="?action=upload" method="post" enctype="multipart/form-data">
                    <input type="file" name="myfile" /><br>
                    <label for="name" >Nom du fichier</label><input type="text" name="name" id="name"/><br>
                    <input type="submit" value="Envoyer le fichier" />
                </form>
                <form action="?action=rename" method="post" enctype="multipart/form-data">
                    <label for="changename">Nom du fichier a modifier : </label><br><input required type="text" name="changename" id="changename"><br>
                    <label for="newname">Nouveau nom : </label><br><input required type="text" name="newname" id="newname"><br>
                    <input type="submit" value="Renommer">
                </form>
                <form action="?action=replace" method="post" enctype="multipart/form-data">
                    <input type="file" name="replacefile" /><br>
                    <label for="name" >Nom du fichier a remplacer</label><input type="text" name="filetoreplace" id="filetoreplace"/><br>
                    <input type="submit" value="Remplacer le fichier" />
                </form>                         
            </div>
            <main>
                <div id="folder">
                    <input id="new_folder" type="submit" value="Créer un nouveau dossier"/>
                    <form id="create_folder" method="post" action="?action=newfolder" >
                        <input type="text" name="new_folder" placeholder="folder_name"/>
                        <input type="submit" value="Créer le dossier !">
                    </form>
                </div>
                <?php if (!empty($files)): ?>
                <div class="allfiles"><?php echo $files ?></div>
                <?php endif; ?>
            </main>            
        </div>
        <div class="footerline"></div>
        <footer>
            <div><span>F</span>iles<span>A</span>dministrator by a random dev</div>
        </footer>
    </body>
</html>