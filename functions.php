<?php
require_once 'config.php';
session_start();
$db = getConnection();

function login($login, $password)
{
    $db = getConnection();
    $password=md5($password);
    $check = $db->prepare("SELECT * FROM user WHERE login=? AND password = ?");
    $check->execute([$login, $password]);
    $user=$check->fetch(PDO::FETCH_ASSOC);
    if ($user){
    $_SESSION['user'] = $user;
    $_SESSION['user_id'] = $user['id'];
    return true;     
    }
     else return false;
    }


function isAuthorized()
{
    return !empty($_SESSION['user']);
} 

function redirect($page)
{
    header("Location: $page.php");
    die;
}


function getConnection() {
     try {
    $connect_str = DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME;
    $db = new PDO ($connect_str, DB_USER, DB_PASS);
    return $db;
}
catch (PDOExeption $e) {
    die("error: " . $e->getMessage());
}
}