<?php

require_once('model/db.php');
require_once('model/filer.php');

function new_folder($newfolder){
    $folderpath = 'uploads/'. $_SESSION['id'] . '/' . $newfolder;

    if(!empty(check_folder_name($newfolder))){
        return 'name already used';
    }
    $dbh = get_dbh();
    $q = "INSERT INTO `folders` (`user_id`, `foldername`, `folderpath`) VALUES (:userid, :foldername, :folderpath);";
            $statement = $dbh->prepare($q);
            $statement->execute([
                'userid' => $_SESSION['id'],
                'foldername' => $newfolder,
                'folderpath' => $folderpath,
            ]);


    $req = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `foldername` = :foldername');
    $req->execute(array(
        'userid' => $_SESSION['id'],
        'foldername' => $newfolder));

    $folderinfo = $req->fetch();
    $folderid = 'uploads/'. $_SESSION['id'] . '/' . $folderinfo['id'];
    mkdir($folderid);

    $req = $dbh->prepare('UPDATE `folders` SET `folderpath` = :newpath WHERE `foldername` =:foldername AND `user_id` =:userid');
    $req->execute(array(
        'userid' => $_SESSION['id'],
        'foldername' => $newfolder,
        'newpath' => $folderid));

    $log_info = ' => user ' . $_SESSION['username'] . ' create the folder "' . $newfolder . '".' . "\n";
    write_log('access.log', $log_info);

    return 'folder created';
}

function check_folder_name($foldername){
    $dbh = get_dbh();

    $req = $dbh->prepare('SELECT `foldername` FROM `folders` WHERE `user_id` =:userid AND `foldername` =:foldername');
    $req -> execute([
        'userid' => $_SESSION['id'],
        'foldername'=> $foldername,
    ]);
    $resultat = $req->fetch();
    return $resultat;
}

function display_folders(){
    $files = "";
    $dbh = get_dbh();
    $req = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `folder_id` IS NULL');
    $req->execute(array(
        'userid' => $_SESSION['id']));

    while ($donnees = $req->fetch()){
        $request = $dbh->prepare('SELECT * FROM `files` WHERE `user_id` = :userid AND `folder_id` = :folderid ');
        $request->execute(array(
            'userid' => $_SESSION['id'],
            'folderid' => $donnees['id'],
            ));

        $q = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `folder_id` IS NULL');
        $q->execute(array(
            'userid' => $_SESSION['id']));

        $select = '<select name="movefolder"><option value="uploads/'. $_SESSION['id'] .'">Dossier Principal</option>';
        while ($movefolders = $q->fetch()){
            $select = $select . '<option value="' . $movefolders['folderpath'] . '">' . $movefolders['foldername'] . '</option>';
        }
        $select = $select . '</select>';

            $files = $files . '<div class="folder"><div class="hidden closedimg"><img src="assets/folder.svg" alt="folderclosed"><input class="open" type="submit" name="openfolder" value="Ouvrir le dossier"></div><div class="folder_info">' . $donnees['foldername'] . '<form action="index.php?action=deletefolder" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$donnees['folderpath'].'" name="foldertodelete"><label for="'.$donnees['folderpath'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['folderpath'].'" class="hidden" type="submit" value="Supprimer"></form><form action="index.php?action=renamefolder" method="post" enctype="multipart/form-data"><input class="hidden foldertomove" required type="text" value="'.$donnees['foldername'].'" name="foldertorename" ><input type="text" name="newfoldername" placeholder="Entrez un nouveau nom"><br><input type="submit" value="Renommer"></form><form action="?action=movefolder" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$donnees['folderpath'].'" name="foldertomove">'. $select .'<input type="submit" value="Deplacer le dossier"/></form><input type="submit" class="close" name="closefolder" value="Fermer le dossier"></div><div class="folder_files">';
            
            while($test = $request->fetch()){
                $files = $files . display_files($test);
            }
            $files = $files . '</div>';

        $requ = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `folder_id` = :folderid ');
        $requ->execute(array(
            'userid' => $_SESSION['id'],
            'folderid' => $donnees['id'],
            ));

        while($folderinfolder = $requ->fetch()){
            
            $request = $dbh->prepare('SELECT * FROM `files` WHERE `user_id` = :userid AND `folder_id` = :folderid ');
            $request->execute(array(
                'userid' => $_SESSION['id'],
                'folderid' => $folderinfolder['id'],
                ));

            $q = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `folder_id` IS NULL');
            $q->execute(array(
                'userid' => $_SESSION['id']));

            $select = '<select name="movefolder"><option value="uploads/'. $_SESSION['id'] .'">Dossier Principal</option>';
            while ($movefolders = $q->fetch()){
                $select = $select . '<option value="' . $movefolders['folderpath'] . '">' . $movefolders['foldername'] . '</option>';
            }
            $select = $select . '</select>';

                $files = $files . '<div class="folder"><div class="hidden closedimg"><img src="assets/folder.svg" alt="folderclosed"><input class="open" type="submit" name="openfolder" value="Ouvrir le dossier"></div><div class="folder_info">' . $folderinfolder['foldername'] . '<form action="index.php?action=deletefolder" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$folderinfolder['folderpath'].'" name="foldertodelete"><label for="'.$folderinfolder['folderpath'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$folderinfolder['folderpath'].'" class="hidden" type="submit" value="Supprimer"></form><form action="index.php?action=renamefolder" method="post" enctype="multipart/form-data"><input class="hidden foldertomove" required type="text" value="'.$folderinfolder['foldername'].'" name="foldertorename"><input type="text" name="newfoldername" placeholder="Entrez un nouveau nom"><br><input type="submit" value="Renommer"></form><form action="?action=movefolder" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$folderinfolder['folderpath'].'" name="foldertomove">'. $select .'<input type="submit" class="close" value="Deplacer le dossier"/></form><input type="submit" name="closefolder" value="Fermer le dossier"></div><div class="folder_files">';
                
                while($test = $request->fetch()){
                    $files = $files . display_files($test);
                }
                $files = $files . '</div></div><br>';
        }
        $files = $files . '</div><br>';
    }

    $sth = $dbh->prepare('SELECT * FROM `files` WHERE `user_id` = :userid AND `folder_id` IS NULL');
    $sth->execute(array(
        'userid' => $_SESSION['id'],
        ));
        while($random = $sth->fetch()){
            $files = $files . display_files($random);
        }        
    return $files; 
}

function display_files($donnees){
    
        $dbh = get_dbh();
        $req = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid ');
        $req->execute(array(
            'userid' => $_SESSION['id']));

        $select = '<select name="move_to"><option value="uploads/'. $_SESSION['id'] .'">Dossier Principal</option>';
        while ($movefiles = $req->fetch()){
            $select = $select . '<option value="' . $movefiles['folderpath'] . '">' . $movefiles['foldername'] . '</option>';
        }
        $select = $select . '</select>';
        

    if($donnees['type'] =='image'){
        $file_displayed = '<div class="files"><img src="' .$donnees['filepath']. '" alt="' .$donnees['filename']. '"/><p>' .$donnees['filename']. '</p><a href="' .$donnees['filepath']. '"
        download="' .$donnees['filename']. '"><img class="trash" src="assets/dl.svg" alt="trash"></a><form action="?action=delete" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filename'].'" name="filetodelete" ><label for="'.$donnees['filename'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['filename'].'" class="hidden" type="submit" value="Supprimer"></form><form action="?action=movefile" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$donnees['filepath'].'" name="filetomove" id="filetomove">'. $select .'<input type="submit" value="Deplacer le fichier"/></form></div>';
    }
    elseif($donnees['type'] =='text'){
        $file_displayed =  '<div class="files"><img class="imgtxt" src="assets/imgtxt.png" alt="doctext"><p>' .$donnees['filename']. '</p><a href="' .$donnees['filepath']. '"
        download="' .$donnees['filename']. '"><img class="trash" src="assets/dl.svg" alt="trash"></a><form action="?action=delete" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filename'].'" name="filetodelete"><label for="'.$donnees['filename'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['filename'].'" class="hidden" type="submit" value="Supprimer"></form><form action="?action=movefile" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" value="'.$donnees['filepath'].'" name="filetomove" >'. $select .'<input type="submit" value="Deplacer le fichier"/></form><div><input type="submit" class="changetxt" name="changetxt" value="Modifier le contenu"><form class="hidden modiftxt" action="?action=changetxt" method="post" enctype="multipart/form-data" ><input class="hidden" required type="text" value="'.$donnees['filepath'].'" name="filetochange" ><textarea name="txt_content" rows="20" cols="100">' . file_get_contents($donnees['filepath']) . '</textarea><input name="modifthetxt" type="submit" value="Modifier !"></form></div></div>';
    }
    elseif($donnees['type'] =='audio'){
        $file_displayed =  '<div class="files"><audio controls src="' .$donnees['filepath']. '"></audio><p>' .$donnees['filename']. '</p><a href="' .$donnees['filepath']. '"
        download="' .$donnees['filename']. '"><img class="trash" src="assets/dl.svg" alt="trash"></a><form action="?action=delete" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filename'].'" name="filetodelete" ><label for="'.$donnees['filename'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['filename'].'" class="hidden" type="submit" value="Supprimer"></form><form action="?action=movefile" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filepath'].'" name="filetomove" >'. $select .'<input type="submit" value="Deplacer le fichier"/></form></div>';
    }
    elseif($donnees['type'] == 'video'){
        $file_displayed =  '<div class="files"><video width="300" controls src="' .$donnees['filepath']. '"></video><p>' .$donnees['filename']. '</p><a href="' .$donnees['filepath']. '"
        download="' .$donnees['filename']. '"><img class="trash" src="assets/dl.svg" alt="trash"></a><form action="?action=delete" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filename'].'" name="filetodelete" ><label for="'.$donnees['filename'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['filename'].'" class="hidden" type="submit" value="Supprimer"></form><form action="?action=movefile" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filepath'].'" name="filetomove">'. $select .'<input type="submit" value="Deplacer le fichier"/></form></div>'; 
    }
    else{
        $file_displayed =  '<div class="files"><embed src="' .$donnees['filepath']. '"></embed><p>' .$donnees['filename']. '</p><a href="' .$donnees['filepath']. '"
        download="' .$donnees['filename']. '"><img class="trash" src="assets/dl.svg" alt="trash"></a><form action="?action=delete" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filename'].'" name="filetodelete" ><label for="'.$donnees['filename'].'"><img class="trash" src="assets/remove_task.png" alt="trash"></label><input id="'.$donnees['filename'].'" class="hidden" type="submit" value="Supprimer"></form><form action="?action=movefile" method="post" enctype="multipart/form-data"><input class="hidden" required type="text" 
        value="'.$donnees['filepath'].'" name="filetomove">'. $select .'<input type="submit" value="Deplacer le fichier"/></form></div>';        
    }
    return $file_displayed;
}

function delete_folder($foldertodelete){
    $dbh = get_dbh();

    $req = $dbh->prepare('SELECT * FROM `folders` WHERE `user_id` = :userid AND `folderpath` =:folderpath');
    $req->execute(array(
        'userid' => $_SESSION['id'],
        'folderpath' => $foldertodelete,
        ));
    $path=$req->fetch();

    $request = $dbh->prepare('SELECT `filename` FROM `files` WHERE `user_id` = :userid AND `folder_id` =:folderid');
    $request->execute(array(
    'userid' => $_SESSION['id'],
    'folderid' => $path['id'],
    ));
    $files=$request->fetchAll();
    foreach($files as $key){
        delete_file($key['0']);
    }

    rmdir($path['folderpath']);               
    $q = "DELETE from `folders` WHERE `folderpath`=:foldertodelete AND `user_id` =:userid";
    $statement = $dbh->prepare($q);
    $statement->execute([
        'foldertodelete' => $foldertodelete,
        'userid' => $_SESSION['id'],
    ]);

    $log_info =' => user ' . $_SESSION['username'] . ' delete the folder "' . $path['foldername'] . '".' . "\n";
    write_log('access.log', $log_info);

    return 'folder deleted';
}

function rename_folder($data){
    $dbh = get_dbh();

    $req = $dbh->prepare('SELECT `foldername` FROM `folders` WHERE `user_id` =:userid AND `foldername` =:foldername');
    $req -> execute([
        'userid' => $_SESSION['id'],
        'foldername'=> $data['newfoldername']
        ]);
    $result = $req->fetch();
    if($result !== false){
        return 'name already used';
    }
    else{
        $d = "UPDATE `folders` SET `foldername` = :newfoldername  WHERE `foldername` =:foldername AND `user_id` =:userid";
        $statement = $dbh->prepare($d);
        $statement->execute([
            'foldername' => $data['foldertorename'],
            'userid' => $_SESSION['id'],
            'newfoldername' => $data['newfoldername'],
        ]);

            $log_info = ' => user ' . $_SESSION['username'] . ' renamed the folder "' . $data['foldertorename'] . '" to "' . $data['newfoldername'] .'".' . "\n";
            write_log('access.log', $log_info);
    }

   return 'folder renamed';
}

function move_file($data){

    $dbh = get_dbh();

    $oldpath = $data['filetomove'];
    $newpath = $data['move_to'] . '/' . basename($data['filetomove']);
    if($data['move_to'] == 'uploads/'.$_SESSION['id']){
        $newfolderid = null;
    }
    else{
        $newfolderid = basename($data['move_to']);
    }

    $d = "UPDATE `files` SET `folder_id` = :newfolderid, `filepath` = :newpath  WHERE `filepath` =:filepath AND `user_id` =:userid";
    $statement = $dbh->prepare($d);
    $statement->execute([
        'newfolderid' => $newfolderid,
        'newpath'=>$newpath,
        'userid' => $_SESSION['id'],
        'filepath' => $data['filetomove'],
    ]);
    rename($oldpath, $newpath);

    $log_info = ' => user ' . $_SESSION['username'] . ' move the file "' . basename($data['filetomove']) . '" to "' . $newpath .'".' . "\n";
    write_log('access.log', $log_info);

    return 'file moved';
}

function move_folder($data){
    $dbh = get_dbh();

    $oldpath = $data['foldertomove'];
    $newpath = $data['movefolder'] . '/' . basename($data['foldertomove']);

    if($data['movefolder'] == 'uploads/'.$_SESSION['id']){
        $newfolderid = null;
    }
    else{
        $newfolderid = basename($data['movefolder']);
    }

    $d = "UPDATE `folders` SET `folderpath` = :newpath, folder_id = :folderid  WHERE `folderpath` =:folderpath AND `user_id` =:userid";
    $statement = $dbh->prepare($d);
    $statement->execute([
        'folderid' => $newfolderid,
        'newpath'=>$newpath,
        'userid' => $_SESSION['id'],
        'folderpath' => $oldpath,
    ]);
    rename($oldpath, $newpath);
    $req = "SELECT * FROM `files` WHERE user_id = :userid and folder_id = :folderid";
    $request = $dbh->prepare($req);
    $request ->execute([
        'folderid' => basename($oldpath),
        'userid' => $_SESSION['id'],
    ]);
    while($donnees = $request->fetch()){        
        $r = "UPDATE `files` SET `filepath`= :newpath WHERE `user_id` =:userid AND `filename`=:filename";
        $sth = $dbh->prepare($r);
        $sth->execute([
            'newpath' =>  $newpath . '/' . $donnees['filename'],
            'filename' => $donnees['filename'],
            'userid' => $_SESSION['id'],            
        ]);
    }

    $log_info = ' => user ' . $_SESSION['username'] . ' move the folder "' . $oldpath . '" to "' . $newpath .'".' . "\n";
    write_log('access.log', $log_info);

    return 'file moved';
}

function modiftxt(){
    $bool = false;
    if(isset($_POST['modifthetxt'])){
        if(isset($_POST['txt_content']) && isset($_POST['filetochange']) && $_POST['filetochange'] != ''){
            $file = $_POST['filetochange'];
            $txt_content = $_POST['txt_content'];
            file_put_contents($file, $txt_content);
            $bool = true;
        }
    }

    $log_info = ' => user ' . $_SESSION['username'] . ' change the content of "' . $file . '"' . "\n";
    write_log('access.log', $log_info);

     return $bool;
} 