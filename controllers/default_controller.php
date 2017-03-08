<?php

require_once('model/db.php');
require_once('model/filer.php');
require_once('model/folder.php');

function home_action(){
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(check_valid_user($_POST) === true){
            connect_user($_POST);
        }
        else{
            $error = check_valid_user($_POST);
        }
    }
    require('views/home.php');
}

function register_action(){
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(check_valid_data($_POST) === true){
            db_insert_new_user($_POST);
            header('Location: ?action=home');
            exit(0);
        }
        else{
            $error = check_valid_data($_POST);
        }
    }
    require('views/register.php');
}

function files_action($error = null){
    if (!empty($_SESSION['username'])){
        $files = display_folders();
        require('views/files.php');
    }
    else{
        write_log('security.log', 'Someone tried to connect' ."\n");
        header('Location: ?action=home');
        exit(0);
    }
}

function deco_action(){
    write_log('access.log', ' => user ' . $_SESSION['username'] . ' loged out.' . "\n");
    session_destroy();
    header('Location: ?action=home');
    exit(0);
}