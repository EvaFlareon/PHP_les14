<?php

session_start();

if (!empty($_SESSION['user'])) {
    header('Location: tasks.php');
}

$errors = [];

$connect = mysqli_connect("localhost", "aevlahina", "neto1705", "aevlahina");
$sql = "select * from user";

if (!empty($_POST)) {
    if (isset($_POST['input'])) {
        $res = mysqli_query($connect, $sql);
        while ($data = mysqli_fetch_array($res)) {
            if ($data['login'] === $_POST['login'] && $data['password'] === $_POST['password']) {
                $_SESSION['user'] = $data['id'];
                header('Location: tasks.php');
            }
        }
    }

    if (isset($_POST['reg'])) {
        $res = mysqli_query($connect, $sql);
        while ($data = mysqli_fetch_array($res)) {
            if ($data['login'] === $_POST['login']) {
                $err = 'Пользователь с таким логином уже существует';
                break;
            } else if ($_POST['login'] === '' || $_POST['password'] === '') {
                $err = 'Не все поля заполнены';
                break;
            } 
            // else {
            //     mysqli_query($connect, "insert into `user`(`login`, `password`) values ('".$_POST['login']."','".$_POST['password']."')");
            //     echo 'Вы успешно зарегистрированы. Войдите под своим логином и паролем';
            // }
        }
        
        if (isset($err)) {
            echo $err;
        } else {
            mysqli_query($connect, "insert into `user`(`login`, `password`) values ('".$_POST['login']."','".$_POST['password']."')");
            echo 'Вы успешно зарегистрированы. Войдите под своим логином и паролем';
        }
    }
}

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<body>
    <h1>Авторизация</h1>
    <form action="" method="POST">
        <label>Логин</label>
        <input type="text" name="login">
        <br>
        <label>Пароль</label>
        <input type="password"  name="password">
        <br>
        <input type="submit" name="input" value="Войти">
        <input type="submit" name="reg" value="Зарегистрироваться">
    </form>
</body>
</html>
