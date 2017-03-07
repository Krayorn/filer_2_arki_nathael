<?php

$dbh = null;

function connect_to_db(){
    global $db_config;
    $dsn = 'mysql:dbname='.$db_config['name'].';host='.$db_config['host'];
    $user = $db_config['user'];
    $password = $db_config['pass'];
    
    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
    }
    
    return $dbh;
}

function get_dbh(){
    global $dbh;
    if ($dbh === null)
        $dbh = connect_to_db();
    return $dbh;
}

function db_insert_new_user($data){
    $dbh = get_dbh();
    $query = "INSERT INTO `users` (`username`, `password`, `email`) VALUES (:username, :password, :email);";
    $sth = $dbh->prepare($query);
    $sth->execute([
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'email' => $data['email'],
                ]);
    $query = "SELECT id FROM `users` WHERE `email` = :email";
    $sth = $dbh->prepare($query);
    $sth->execute([
                    'email' => $data['email'],
                ]);
    $resultat = $sth->fetch();
    mkdir('users/'.$resultat['id']);

    $log_info =' => L\'utilisateur ' . $data['username'] . ' s\'est inscrit.' . "\n";
    write_log('access.log', $log_info);

    return true;
}

function check_valid_data($data){
    if (empty($data['username']) OR empty($data['email']) OR empty($data['password']) or empty($data['verifpassword']))
        return 'fields missings';
    if(check_free('username', $data['username']) !== true)
        return 'take another username';
    if(check_free('email', $data['email']) !== true)
        return 'take another email';
    if($data['password'] !== $data['verifpassword'])
        return 'password don\'t match';
    return true;
}

function check_free($isfree, $data){
    $dbh = get_dbh();
    $query = "SELECT id FROM `users` WHERE `$isfree` = :username";
    $sth = $dbh->prepare($query);
    $sth->execute([
                    'username' => $data,
                ]);
    $resultat = $sth->fetch();
    if(!$resultat){
        return true;
    }
    return false;
}

function check_valid_user($data){
    if (empty($data['username']) OR empty($data['password']))
        return 'fields missings';
    if(does_user_exist($data) !== true)
        return 'username or password false';
    return true;
}

function does_user_exist($data){
    $dbh = get_dbh();
    $query = "SELECT id FROM `users` WHERE `username` = :username AND `password` = :password";
    $sth = $dbh->prepare($query);
    $sth->execute([
                    'username' => $data['username'],
                    'password' => $data['password'],
                ]);
    $resultat = $sth->fetch();
    if (!$resultat){
        return false;
    }
    return true;
}

function connect_user($data){
    $dbh = get_dbh();
    $query = "SELECT * FROM `users` WHERE `username` = :username AND `password` = :password";
    $sth = $dbh->prepare($query);
    $sth->execute([
                    'username' => $data['username'],
                    'password' => $data['password'],
                ]);
    $resultat = $sth->fetch();
    $_SESSION['id'] = $resultat['id'];
    $_SESSION['username'] = $resultat['username'];

    $log_info = ' => L\'utilisateur ' . $_SESSION['username'] . ' s\'est connecté.' . "\n";
    write_log('access.log', $log_info);
}

function give_me_date(){
    $date = date("d-m-Y");
    $heure = date("H:i");
    return $date . " " . $heure;
}

function write_log($file, $text){
    $date = give_me_date();
    $file_log = fopen('logs/' . $file, 'a');
    $log_info = $date . $text;
    fwrite($file_log, $log_info);
    fclose($file_log); 
}