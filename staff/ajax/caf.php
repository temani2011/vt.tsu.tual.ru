<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
if($_SESSION['id'])
{
	if((!empty($_GET['group']) and $_GET['role']=="student"))
	{
		$users = $dal->get_users_role_and_group($_GET['role'], $_GET['group']);
	}
	else
	{
		$users = $dal->get_users_role($_GET['role']);
	}
	if(empty($users))
		{ ?>
			<div class="alert alert-warning" role="alert">
				Не удалось загрузить данные с сервера! Свяжитесь с администратором!
			</div>
			<?php 
		}?>
		<div class="row">
			<div class="col col-md-8 col-lg-9" id="main-column" style="padding-right: 10px;">
				<div class="card">
					<div class="card-status bg-blue"></div>
					<h6 class="card-header" id="caf-header">Кафедра (<?php echo count($users); ?>)</h6>
					<h6 class="card-header card-search">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1" style="background: white;"><i class="fa fa-search" aria-hidden="true"></i></span>
							</div>
							<input type="text" id="search-criteria" class="form-control" placeholder="Поиск" aria-describedby="basic-addon1">
						</div>
					</h6>
					<div class ="card-body" id="content-users">
						<?php foreach ($users as $user){ if($user['role'] == "admin") continue; ?>
							<div class="row row-user">
								<div class="col-2 col-sm-2 text-center pt-2 pl-2 p-0">
									<div class="chat_img" style="width: 75%; margin-left: 10px;">        
										<?php if(!$user['img']) { ?>
											<img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
										<?php } else { ?>
											<img alt="User Pic" src="<?php echo $user['img'];?>"  width="300" height="auto" class="rounded-circle">
										<?php } ?>
									</div>
								</div>
								<div class="col col-sm center-text-block">
									<div class="info">
										<div class="labeled name"><a href="#profile.php?command=select&id=<?php echo $user['id']?>"><?php echo $user['surname'] . " " . $user['name'] . " " . $user['midname'];?></a>
										</div>
										<div class="labeled under"><? $ngr=$dal->get_users_groups($user['id1']); if($user['role']=="student") $role= "Студент " . "(" . $ngr[0]['group_number'] . ")"; else if($user['role']=="teacher") $role= "Преподаватель"; else $role = "Сотрудник кафедры"; echo $role ?></div>
										<div class="labeled under"><?php echo $user['email'];?></div>
									</div>
								</div>
								<div class="settings caf">
									<span class="input-group-addon">
										<button type="button" data-toggle="dropdown" id="btn-settings-caf"><i class="fa fa-ellipsis-v" aria-hidden="true" style="color: #c9c9c9;"></i></button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a class="dropdown-item" uid="<?php echo $user['id'];?>" href="#profile.php?command=update_user&id=<?php echo $user['id'];?>" id="dd-caf-update">Редактировать</a> 
											<? if(($_SESSION['role']=='staff' || $_SESSION['role']=='admin') && $user['id']!=$_SESSION['id']) { ?>
											<a class="dropdown-item" href="#" id="dd-caf-delete" uid="<?php echo $user['id'];?>">Удалить</a>
											<?php } ?>
										</div>
									</span>
								</div>
							</div>
							<?php
						}?>
					</div>
				</div>
			</div>
			<div class="col col-md-4 col-lg-3 rblock" style="padding-left: 10px;">
				<div class="card">
					<div class="card-status bg-blue"></div>
					<h6 class="card-header">Управление</h6>
					<div class="card-body">
						<div class="form-group radio">
	                        <div class="form-label">Деятельность</div>
	                        <div class="custom-controls-stacked">
	                          <label class="custom-control custom-radio">
	                            <input type="radio" class="custom-control-input" name="example-radios" value="student">
	                            <div class="custom-control-label">Студент</div>
	                          </label>
	                          <label class="custom-control custom-radio">
	                            <input type="radio" class="custom-control-input" name="example-radios" value="teacher">
	                            <div class="custom-control-label">Преподаватель</div>
	                          </label>
	                          <label class="custom-control custom-radio">
	                          	<input type="radio" class="custom-control-input" name="example-radios" value="staff">
	                          	<div class="custom-control-label">Сотрудник<br>кафедры</div>
	                          </label>
	                          <label class="custom-control custom-radio">
	                            <input type="radio" class="custom-control-input" name="example-radios" value="all" checked="">
	                            <div class="custom-control-label">Все</div>
	                          </label>
	                        </div>
	                    </div>
	                    <div class="form-group select">
	                    	<div class="form-label">Группы</div>
	                    	<select id="groups" class="form-control custom-select" disabled="true">
	                    		<option selected value ="all"> Все </option>
	                    		<?php
	                    		$groups = $dal->get_users_groups(-1);
	                    		foreach($groups as $group){?>
	                        		<option value="<?php echo $group['group_number']?>"><?php echo $group['group_number']?></option>
	                        	<?php } ?>
	                        </select>
	                    </div>
	                    <div class="form-group radio">
	                        <div class="form-label">Добавить</div>
	                        <div class="custom-controls-stacked">
	                          <label class="custom-control custom-radio">
	                            <input type="radio" class="custom-control-input" name="example-radios2" value="group" checked="true">
	                            <div class="custom-control-label">Группу</div>
	                          </label>
	                          <label class="custom-control custom-radio">
	                            <input type="radio" class="custom-control-input" name="example-radios2" value="user">
	                            <div class="custom-control-label">Пользователя</div>
	                          </label>
	                          <input type="button" class="btn btn-sm btn-block btn-primary mt-3" id="btn-create-profile" value="Применить">
	                        </div>
	                    </div>
					</div>
				</div>
			</div>
		</div>
<?php 
}
else
{	$err ='
	<div class="alert alert-warning" role="alert">
		У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
	</div>';
	echo $err;
}
?>