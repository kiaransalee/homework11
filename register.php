<?php

require_once 'config.php';
require_once 'functions.php';

if (!empty($_POST)){
$login=$_POST['login'];
$password=md5($_POST['password']);


    $check = $db->prepare("SELECT * FROM user WHERE login=?");
    $check->execute([$login]);
    $result = $check->fetchAll(PDO::FETCH_ASSOC);
   
     if (!empty ($result)) {
        echo "Такой пользователь уже зарегистрирован<br>Попробуйте авторизоваться или придумайте новый логин.<br><a href='./login.php'>Вернуться назад</a>";
    }
    else {
    $sth = $db->prepare("INSERT INTO user(login,password) VALUES (?,?)");
    $sth->execute(["$login","$password"]);
    echo "Вы успешно зарегистрировались, пройдите авторизацию<br><a href='./login.php'>Вернуться назад</a>";
    }
} 
