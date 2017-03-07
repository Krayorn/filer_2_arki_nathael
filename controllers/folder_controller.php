<?php 

require_once('controllers/default_controller.php');
require_once('model/folder.php');

function newfolder_action(){
    $folder_error = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['new_folder'])){
            $folder_error =  new_folder($_POST['new_folder']);
        }
        else{
            $folder_error = "Choose a name !";
        }
    }
    files_action($folder_error);
}

function deletefolder_action(){
    $folder_delete_error ='';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['foldertodelete'])){
            $folder_delete_error = delete_folder($_POST['foldertodelete']);
        }  
    }
    files_action($folder_delete_error);
}

function renamefolder_action(){
    $folder_rename_error ='';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['foldertorename'])
        AND !empty($_POST['newfoldername'])){
            $folder_rename_error = rename_folder($_POST);
        }  
    }
    files_action($folder_rename_error);
}

function movefile_action(){
    $file_moved_error ='';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['move_to'])
        AND !empty($_POST['filetomove'])){
            $file_moved_error = move_file($_POST);
        }  
    }
    files_action($file_moved_error);
}

function movefolder_action(){
    $folder_moved_error ='';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['movefolder'])
        AND !empty($_POST['foldertomove'])){
            $folder_moved_error = move_folder($_POST);
        }  
    }
    files_action($folder_moved_error);    
}