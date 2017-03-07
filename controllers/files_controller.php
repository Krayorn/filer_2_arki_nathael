<?php 

require_once('controllers/default_controller.php');
require_once('model/filer.php');

function upload_action(){
    $upload_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_FILES['myfile']['tmp_name'])){
            if(insert_new_files($_FILES['myfile'], $_POST) === true){
                $upload_error = 'fichier uploadé';
            }
            else{
                $upload_error = insert_new_files($_FILES['myfile'], $_POST);
            }
        }
        else{
            $upload_error = 'file missing';
        }
    }
    files_action($upload_error);
}

function delete_action(){
    $delete_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['filetodelete'])){
            $delete_error = delete_file($_POST['filetodelete']);
        }
        else{
            $delete_error = 'file missing';
        }
    }
    files_action($delete_error);
}

function rename_action(){
    $rename_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['changename'])
        AND !empty($_POST['newname'])) {
            $rename_error = rename_file($_POST);
        }
        else{
            $rename_error = "Fill both input";
        }
    }
    files_action($rename_error);
}

function replace_action(){
    $replace_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['filetoreplace'])){
            $replace_error = replace_file($_POST, $_FILES);
        }
        else{
            $replace_error = "Select a file to replace";
        }
    }    
    files_action($replace_error);
}