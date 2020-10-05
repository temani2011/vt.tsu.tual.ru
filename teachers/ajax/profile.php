<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$res = array();
$dal = new DAL();
if($_SESSION['id'])
{	
	if (isset($_FILES['file']['tmp_name']))
	{
		$base_folder = $_SERVER['DOCUMENT_ROOT'];
		$user = $dal->get_user($_POST['uid'])[0];
		$old_file = $base_folder . $user['img'];
		$new_file = $base_folder . '/img/users/' .  $_FILES['file']['name'];
		if(file_exists($old_file) && $user['img']) 
		{   
			if(!unlink($old_file)){ echo "Не удалось удалить старый файл"; exit; }
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $new_file);
		if($dal->upd_new_user_img($user['id'], '/img/users/' . $_FILES['file']['name'])) echo 'Файл успешно загружен!';
		exit();
	}
	if(isset($_GET['id']) || isset($_GET['command'])) 
	{
		$user_id =  $_GET['id'];
		$command = $_GET['command'];
		switch ($command) {
			case 'select':
				$user = $dal->get_users_groups($user_id)[0];
				if($user['role'] != 'student') $user = $dal->get_user($user_id)[0]; ?>
				<div class="row mx-auto" style="justify-content: center;">
					<div class="col col-md-8 col-lg-8" style="padding-right: 10px">
						<div class="card panel-info">
							<div class="card-status bg-blue" style="position: inherit;"></div>
							<div class="card-header">
								<h3 class="card-title"><?php echo $user["surname"] . " " . $user["name"] . " " . $user["midname"] ?></h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-3 col-lg-3 mb-3" align="center"> 
										<?php if(!$user['img']) {?>
											<img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
										<?php } else { ?>
											<img alt="User Pic" src="<?php echo $user['img'];?>"  width="300" height="auto" class="rounded-circle"> <?php } ?>
											<?php if($user_id == $_SESSION['id']){ ?>
												<div class="row justify-content-center mt-3">
													<form name="uploaduserimage" method="post" enctype="multipart/form-data">
														<input type="file" id="user-img-upload" accept="image/*"/>
														<label for="user-img-upload" class="custom-file-upload user-img-upload">
															<i class="fa fa-cloud-upload"></i> Обновить
														</label>
													</form>
												</div>
											<?php } ?>
										</div>
										<div class=" col-md-9 col-lg-9 "> 
											<table class="table table-user-information">
												<tbody>
													<tr><td>Деятельность</td>
														<td><?php $role = $user['role']; if($role == 'student') echo 'Студент'; else if($role == 'staff') echo "Сотрудник кафедры"; else if($role == 'teacher') echo "Преподаватель"; else echo "Администратор"; ?></td>
													</tr>
													<?php if($role == 'student') { ?>
														<tr>
															<td>Группа:</td>
															<td><?php echo $user['group_number']?></td>
														</tr>	
														<tr>
															<td>Специальность:</td>
															<td><?php echo $user['specialty']?></td>
														</tr>
													<?php } ?>
													<tr>
														<td>Дата рождения</td>
														<td><?php $date = new DateTime($user['birth_date']); $date=$date->format('d.m.Y'); echo $date; ?></td>
													</tr>
													<tr>
														<td>Дата регистрации</td>
														<td><?php $date = new DateTime($user['date_of_registration']); $date=$date->format('d.m.Y'); echo $date;?></td>
													</tr>
													<tr>
														<td>Город</td>
														<td><?php echo $user['city'];?></td>
													</tr>
													<tr>
														<td>Email</td>
														<td><a href="mailto:<?php echo $user['email'];?>"><?php echo $user['email'];?></a></td>
													</tr>
													<tr><td>Телефон</td>
														<td><?php echo $user['phone'];?></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary" href="mailto:<?php echo $user['email'];?>"><i class="fa fa-envelope" style="color: white"></i></a>
									<span class="pull-right">
										<!--<a href="#profile.php?command=update_user&id=<?php echo $user['id'];?>" data-original-title="Edit this user" data-toggle="tooltip" uid="<?php echo $user_id; ?>" id="btn-update-profile-user" type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil-square-o" style="color: white"></i></a>-->
									</span>
								</div>
							</div>
						</div>
						<div class="col col-md-3 col-lg-3 rblock" style="padding-left: 10px;">
							<div class="card panel-select">
								<div class="card-status bg-blue" style="position: inherit;"></div>
								<div class="card-header">Меню</div>
								<div class="card-body p-0">
									<nav class="profile-side-menu">
										<ul class="list-unstyled m-0">
											<li><a href="#">Информация</a><li>
											<li><a href="#album.php?id=<?php echo $user_id ?>">Альбом</a><li>
											<li><a href="#blog.php?id=<?php echo $user_id ?>">Блог</a><li>	
										</ul>
									</nav>
								</div>
							</div>
						</div>
			</div>
			<?php exit;
		}
	}
}	
else
{
	$err ='<div class="alert alert-warning" role="alert">
				У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
		   </div>';
	echo $err;
}
?>