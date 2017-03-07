<?php

require_once('model/db.php');

function insert_new_files($data, $post =""){
    $dbh = get_dbh();
    $extensions = array('image', 'text', 'application', 'audio');
    $extension = strrchr(basename($data['name']), '.');
    
    if(!empty($post['name'])){
        $filename = $post['name'] . $extension;
    }
    else{
        $filename = basename($data['name']);
    }
    $type = dirname(mime_content_type($data['tmp_name']));
    $filepath = 'users/' . $_SESSION['id'] .'/' . $filename;
    if(!empty(check_file_name($filename))){
        return 'name already used';
    }
    elseif(in_array($type, $extensions) === false){
        return 'extension is not allowed';
    }
    else{
        move_uploaded_file($data['tmp_name'], $filepath);
        $q = "INSERT INTO `files` (`user_id`, `filepath`, `filename`, `type`) VALUES (:userid, :filepath, :filename, :type);";
        $statement = $dbh->prepare($q);
        $statement->execute([
            'userid' => $_SESSION['id'],
            'filepath' => $filepath,
            'filename' => $filename,
            'type' => $type,
        ]);

        $log_info = ' => L\'utilisateur ' . $_SESSION['username'] . ' a uploadé un fichier "' . $filename . '" de type "' . $type . '".' . "\n";
        write_log('access.log', $log_info);

        return true;
    }
}

function check_file_name($filename){
    $dbh = get_dbh();

    $req = $dbh->prepare('SELECT `filename` FROM `files` WHERE `user_id` =:userid AND `filename` =:filename');
    $req -> execute([
        'userid' => $_SESSION['id'],
        'filename'=> $filename,
    ]);
    $resultat = $req->fetch();
    return $resultat;
}

function delete_file($filetodelete){
    $dbh = get_dbh();
    $req = $dbh->prepare('SELECT `filepath` FROM `files` WHERE `user_id` = :userid AND `filename` =:filename');
    $req->execute(array(
        'userid' => $_SESSION['id'],
        'filename' => $filetodelete
        ));
    $path=$req->fetch();
    unlink($path['filepath']);                
    $q = "DELETE from `files` WHERE `filename`=:filetodelete AND `user_id` =:userid";
    $statement = $dbh->prepare($q);
    $statement->execute([
        'filetodelete' => $filetodelete,
        'userid' => $_SESSION['id'],
    ]);

    $log_info =' => L\'utilisateur ' . $_SESSION['username'] . ' a supprimé le fichier "' . $filetodelete . '".' . "\n";
    write_log('access.log', $log_info);

    return 'file deleted';
}

function rename_file($data){
    $dbh = get_dbh();
    $changename = $data['changename'];
    $newname = $data['newname'];
    $extension = strrchr($changename, '.');


    $req = $dbh->prepare('SELECT `filename` FROM `files` WHERE `user_id` =:userid AND `filename` =:filename');
    $req -> execute([
        'userid' => $_SESSION['id'],
        'filename'=> $changename,   
        ]);
    $result = $req->fetch();
    if($result === false){
        return 'file not found';
    }
    else{
        $req = $dbh->prepare('SELECT `filename` FROM `files` WHERE `user_id` =:userid AND `filename` =:filename');
        $req -> execute([
            'userid' => $_SESSION['id'],
            'filename'=> $newname
            ]);
        $result = $req->fetch();
        if($result !== false){
            return 'name already used';
        }
        else{
            $oldpath = 'users/' . $_SESSION['id'] . '/' . $changename;
            $newpath = 'users/' . $_SESSION['id'] . '/' . $newname . $extension;
            $d = "UPDATE `files` SET `filepath` = :newpath, `filename` = :newname  WHERE `filename` =:changename AND `user_id` =:userid";
            $statement = $dbh->prepare($d);
            $statement->execute([
                'changename' => $changename,
                'newpath'=>$newpath,
                'userid' => $_SESSION['id'],
                'newname' => $newname . $extension,
            ]);
            rename($oldpath, $newpath);

            $log_info = ' => L\'utilisateur ' . $_SESSION['username'] . ' a renommé le fichier "' . $changename . '" en "' . $newname . $extension .'".' . "\n";
            write_log('access.log', $log_info);

            return "file renamed";
        }
    }
}

function replace_file($data, $files){
    delete_file($data['filetoreplace']);
    insert_new_files($files['replacefile']);
    return 'file replaced';
}