<?php
require_once 'functions.php';
if (isAuthorized()) {
    redirect('tasks');
}

$errors = [];
if (isset($_POST['auth'])) {
    //Производим вход
    if (login($_POST['login'], $_POST['password'])) {
        header('Location: tasks.php');
        die;
    } else {
        $errors[] = 'Неверный логин или пароль';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <title>Авторизация</title>
</head>
<body>
<section id="login">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-wrap">
                    <h1>Авторизация</h1>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <ul><?= $error ?></ul>
                        <?php endforeach; ?>
                    </ul>
                    <form method="POST">
                        <input type="hidden" name="auth" value="auth">
                        <div class="form-group">
                            <label for="lg" class="sr-only">Логин</label>
                            <input type="text" placeholder="Логин" name="login" id="lg" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="key" class="sr-only">Пароль</label>
                            <input type="password" placeholder="Пароль" name="password" id="key" class="form-control">
                        </div>
                        <input type="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Войти">
                    </form>

                    <hr>
                    
                </div>
                <h1>Регистрация</h1>
                <form action="register.php" method="POST">
                    <div class="form-group">
                        <label for="lg" class="sr-only">Логин</label>
                        <input type="text" placeholder="Логин" name="login" id="login" class="form-control">
                    </div>
                    <div class="form-group">
                            <label for="key" class="sr-only">Пароль</label>
                            <input type="password" placeholder="Пароль" name="password" id="key" class="form-control">
                        </div>
                    <input type="submit" id="btn-login" class="btn btn-success btn-lg btn-block" value="Зарегистрироваться">
                </form>
                
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</section>
</body>
</html>