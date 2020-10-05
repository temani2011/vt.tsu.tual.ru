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
<body><!--Навигационная панель-->
<?php if(isset($_SESSION['id'])) { ?>
	<div id ="header">
		<nav class="navbar navbar-expand navbar-dark bg-primary">
			<a class="sidebar-toggle mr-3" href="javascript:void()" id="sidebarCollapse"><i class="fa fa-bars" style="color:white"></i></a>	
				<img src="/img/logo/tulgu_logo.png" height="22" href="#" id="tulgu-logo1"/>
				<img src="/img/logo/asd.png" height="22" href="#" id="tulgu-logo2"/>
			</a>
			<div class="navbar-collapse collapse">
				<ul class="navbar-nav ml-auto">
					<!--<li class="nav-item"><a href="#" class="nav-link"><i class="fa fa-envelope"></i> 5</a></li>
					<li class="nav-item"><a href="#" class="nav-link"><i class="fa fa-bell"></i> 3</a></li>-->
					<li class="nav-item dropdown">
						<a href="#" id="dd_user" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo ($_SESSION["surname"] ." " . mb_substr($_SESSION["name"], 0,1,"UTF-8") .". " . mb_substr($_SESSION["midname"], 0,1,"UTF-8"). "."); ?>	</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd_user">
							<a href="#profile.php?command=select&id=<?php echo $_SESSION['id']?>" class="dropdown-item">Профиль</a>
							<a href="#" class="dropdown-item" id = "a-out">Выйти</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>
		<hr id="line"/>
	</div>
<?php } 
else 
{ ?>
	<div id ="header">
		<nav class="navbar navbar-expand navbar-dark bg-primary">
			<link rel="stylesheet" href="css/header.css">
			<img src="/img/logo/tulgu_logo.png" height="22" href="#" id="tulgu-logo1"/>
		</nav>
		<hr id="line"/>
	</div>
<?php } ?>
</body>
</html>