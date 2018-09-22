<?php
require_once 'functions.php';
require_once 'config.php';

if (isAuthorized()) {
   

$user_id = $_SESSION['user_id'];
$user= $_SESSION['user'];

if(isset($_POST['select'])){
    $select = $_POST['select'];
    foreach ($select as $key=>$author) {
        if(!empty($author)){
        $de=$db->prepare("UPDATE task SET assigned_user_id=? WHERE id=? LIMIT 1");
        $de->execute([$author,$key]);
    }
    }
}

if(isset($_POST['add'])) {
    $description = $_POST['name'];
    $is_done = 0;
    $date_added = $_POST['date'];
    
    $au = $db->prepare("INSERT INTO task(user_id,assigned_user_id,description,is_done,date_added) VALUES (?,?,?,?,?)");
    $au->execute([$user_id,$user_id,$description,$is_done,$date_added]);
}

if(!empty($_POST['done'])){
    $form_info=$_POST['done'];
    foreach($form_info as $key=>$checkbox){
      $do = $db->prepare("UPDATE task SET is_done=1 WHERE assigned_user_id=? AND id=? LIMIT 1");
      $do->execute([$user_id,$key]); 
    }
}

if(!empty($_POST['delete'])){
    $form_info=$_POST['delete'];
    foreach($form_info as $key=>$checkbox){
      $do = $db->prepare("DELETE FROM task WHERE assigned_user_id = ? AND id=? LIMIT 1");
      $do->execute([$user_id,$key]); 
    }
}

?>


<!DOCTYPE HTML>
<html>
<head charset="utf-8">
    <title>Список дел</title>
</head>
<body>
  Добро пожаловать, <?php echo $user['login'];?> <a href="./logout.php">(Выйти)</a>
    <h1>Мой список дел</h1>
        <h3>Актуальные дела
        (<?php 
        $count=$db->prepare("SELECT COUNT(*) from task WHERE assigned_user_id=? AND is_done=0");
        $count->execute([$user_id]);
        $c = $count->fetchAll(PDO::FETCH_ASSOC);
        foreach($c as $numbers){
            foreach($numbers as $number){
            echo $number;
        }
        }
        ?>)</h3>
        <form method="POST">
        <input type="hidden" name="todo">
        <table border=1;>
            <tr align="center" style="font-weight:bold;">
                <td>Что нужно сделать</td>
                <td>Дата</td>
                <td>Уже выполнено?</td>
                <td>Автор</td>
                <td>Делегировать</td>
                <td>Удалить</td>
            </tr>
            <?php
    $tk = $db->prepare("SELECT user.id as author_id, login, task.id as task_id, user_id, assigned_user_id, description, is_done, date_added  FROM task JOIN user ON user.id = task.user_id WHERE assigned_user_id=?  ORDER BY date_added DESC");
    $tk->execute([$user_id]);
    $tasks=$tk->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($tasks)) {
        foreach($tasks as $task) {
            $au = $db->prepare("SELECT * FROM user WHERE id<>?");
            $au->execute([$user_id]);
            $authors=$au->fetchAll(PDO::FETCH_ASSOC);
            $form=0;
            $done="<input type='checkbox' name='done[" . $task['task_id'] . "]'>";
            $delete="<input type='checkbox' name='delete[" . $task['task_id'] . "]'>";
            if(!empty($authors)) {
            foreach($authors as $author) {
                $form1='<option value="';
                $form2='">';
                $form.=$form1.$author['id'].$form2.$author['login']."</option>";
               
            }
            }
            
            if ($task['is_done'] == 0) {
     echo "<tr><td>".$task['description']."</td><td>".$task['date_added']."</td><td style='text-align:center;'>".$done."</td><td>".$task['login']."</td><td><select name='select[".$task['task_id']."]'><option></option>".$form."</select></td><td>".$delete."</td></tr>";
   }
        
    }
    }
            ?>
            
        </table>
        <button type="submit">Сохранить изменения</button>
        </form>
        <h4>Добавить пункт</h4>
        <form method="POST">
        <input type="hidden" name="add">
        <input type="text" name="name" placeholder="Что нужно сделать?">
        <input type="date" name="date">
        <button type="submit">Добавить</button>
        </form>
        
        <h4>Были делегированы:</h4>
        <table border=1>
            <?php
               $delegate = $db->prepare("SELECT * FROM task WHERE user_id=? AND assigned_user_id<>?");
               $delegate->execute([$user_id,$user_id]);
               $delegate_task = $delegate->fetchAll(PDO::FETCH_ASSOC);
               if(!empty($delegate_task)){
               foreach ($delegate_task as $de_task){
                   $delegate_authors = $db->prepare("SELECT login FROM user WHERE id=?");
                   $delegate_authors ->execute([$de_task['assigned_user_id']]);
                   $de_authors = $delegate_authors->fetchAll(PDO::FETCH_ASSOC);
                   foreach ($de_authors as $de_author) {
                   echo "<tr><td>".$de_task['description']."--->".$de_author['login']."</td></tr>";
               }
               }
               }
            ?>
        </table>
        
        <h3>Уже выполнено
        (<?php 
        $count=$db->prepare("SELECT COUNT(*) from task WHERE assigned_user_id=? AND is_done=1");
        $count->execute([$user_id]);
        $c = $count->fetchAll(PDO::FETCH_ASSOC);
        foreach($c as $numbers){
            foreach($numbers as $number){
            echo $number;
        }
        }
        ?>)</h3>
        <form method="POST">
            <input type="hidden" name="todo">
        <table border=1 style="color:gray;">
            <tr align="center" style="font-weight:bold;">
                <td>Что нужно сделать</td>
                <td>Дата</td>
                <td>Удалить</td>
            </tr>
            <?php
    $tk = $db->prepare("SELECT * FROM task WHERE assigned_user_id=?");
    $tk->execute([$user_id]);
    $tasks=$tk->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($tasks)) {
        foreach($tasks as $task) {
            $delete="<input type='checkbox' name='delete[" . $task['id'] . "]'>";
            if ($task['is_done'] == 1) {
     echo "<tr><td>".$task['description']."</td><td>".$task['date_added']."</td><td>".$delete."</td></tr>";
   }
        
    }
    }
            ?>
            </table>
            <button type="submit">Сохранить изменения</button>
            </form>
</body>
</html>
<?php 
}else{
    redirect('login');
    }?>