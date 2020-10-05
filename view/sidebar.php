<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/index.css">
</head>
<body><!--Боковая панель-->
    <nav class="sidebar sidebar-dark bg-dark" id="sidebar">
        <div>
            <gcse:search></gcse:search>
        </div>
        <ul class="list-unstyled" id="list">
            <li><a href="#main.php"><i class="fa fa-home" aria-hidden="true"></i> Главная </a></li>
            <li><a href="#news.php"><i class="fa fa-fw fa-newspaper-o"></i> Новости </a></li>
            <li><a href="#caf.php"><i class="fa fa-fw fa-graduation-cap"></i> Кафедра </a></li>
            <li><a href="#schedule.php"><i class="fa fa-fw fa-calendar"></i> Расписание </a></li>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff') { ?>
            <li><a href="#progress.php"><i class="fa fa-tasks"></i> Успеваемость </a></li>
            <li><a href="#messages.php"><i class="fa fa-envelope"></i> Сообщения </a></li>
            <?php } ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'staff') { ?>
            <li><a href="#messages.php"><i class="fa fa-envelope"></i> Сообщения </a></li>
            <?php } ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'student') { ?>
            <li><a href="#journal.php"><i class="fa fa-book"></i> Журнал </a></li>
            <?php } ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin') { ?>
            <li><a href="#docs.php"><i class="fa fa-fw fa-file"></i> Документы </a></li>
        <?php }?>
            <li><a href="#" id="go-up-down"><i class="fa fa-arrow-up" aria-hidden="true"></i> Вверх </a></li>
        </ul>
    </nav>
</body>
</html>