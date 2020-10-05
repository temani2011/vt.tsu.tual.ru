<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT']. '/vendor/PHPMailer/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT']. '/vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT']. '/vendor/PHPMailer/PHPMailer/src/SMTP.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
session_start();
$res = array();
$dal = new DAL();
if($_SESSION['id'])
{	
	function generatePassword($length = 8) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);

		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}

		return $result;
	}
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
	if(isset($_POST['id']) || isset($_POST['command']))
	{
		$user_id =  $_POST['id'];
		$command = $_POST['command'];
		switch ($command) {
			case 'create_user':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					$date = new DateTime($arr_t[3]); 
					$date=$date->format('Y-m-d');
					$arr_t[3] = $date;
					/*generate password*/
					if($arr_c[0] == 'student') $password = '123';
					else $password = '321';
					$password = generatePassword(8);
					$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
					try {
					    //Server settings
					    $mail->isSMTP();                                      // Set mailer to use SMTP
					    $mail->Host = 'smtp.yandex.ru;smtp.mail.ru';  // Specify main and backup SMTP servers
					    $mail->SMTPAuth = true;                               // Enable SMTP authentication
					    $mail->Username = 'tulgucafvt@yandex.ru';                 // SMTP username
					    $mail->Password = '91378624';                           // SMTP password
					    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					    $mail->Port = 587;                                    // TCP port to connect to

					    //Recipients
					    $mail->setFrom('tulgucafvt@yandex.ru', 'Tulgu');
					    $mail->addAddress($arr_t[5]);     // Add a recipient

					    //Attachments
					    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
					    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
					    $body = 
					    "
					    <h4>Ваш email: </h4> <p>".$arr_t[5]."</p></br> 
					    <h4>Ваш новый пароль: </h4> <p>".$password."</p></br>
					    ";

					    //Content
					    $mail->isHTML(true);                                  // Set email format to HTML
					    $mail->Subject = 'Вы зарегестрированны на кафедре ВТ ТулГУ!';
					    $mail->Body    = $body;
					    $mail->AltBody = strip_tags($body);

					    $mail->send();
					    echo 'Message has been sent';
					} catch (Exception $e) {
						echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
					}
					$password = md5($password);
					$user_count = 0;
					if(($user_count = count($dal->get_users_role('all'))) > 0){
						$user_count++;
						if(mkdir($_SERVER['DOCUMENT_ROOT'] . "/img/" . $user_count, 0700))
							if($dal->set_new_user($user_count, $arr_t[0], $arr_t[1], $arr_t[2], $arr_t[3], $arr_t[6], $arr_t[5], $password, $arr_t[4], $arr_c[0], '1', $arr_c[1]))
								if($dal->set_new_blog($user_count)) { echo 'Пользователь успешно создан!'; break; }
					}
				}
				echo 'Не удалось создать пользователя!';
				break;

			case 'create_group':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					if($dal->set_new_group($arr_t[0], $arr_t[1], $arr_c[0]))
					{ echo 'Группа успешно создан!'; break; }
				}
				echo 'Не удалось создать группу!';
				break;

			case 'update_user':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					$date = new DateTime($arr_t[3]);
					$date=$date->format('Y-m-d');
					$arr_t[3] = $date;
					/*generate password*/
					if($arr_c[0] == 'student') $password = '123';
					else $password = '321';			
					$password = generatePassword(8);		
					/*sent email with password*/
					$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
					try {
					    //Server settings
					    $mail->isSMTP();                                      // Set mailer to use SMTP
					    $mail->Host = 'smtp.yandex.ru;smtp.mail.ru';  // Specify main and backup SMTP servers
					    $mail->SMTPAuth = true;                               // Enable SMTP authentication
					    $mail->Username = 'tulgucafvt@yandex.ru;';                 // SMTP username
					    $mail->Password = '91378624';                           // SMTP password
					    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					    $mail->Port = 587;                                    // TCP port to connect to

					    //Recipients
					    $mail->setFrom('tulgucafvt@yandex.ru', 'Tulgu');
					    $mail->addAddress($arr_t[5]);     // Add a recipient

					    //Attachments
					    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
					    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
					    $body = 
					    "
					    <h4>Ваш email: </h4> <p>".$arr_t[5]."</p></br> 
					    <h4>Ваш новый пароль: </h4> <p>".$password."</p></br>
					    ";

					    //Content
					    $mail->isHTML(true);                                  // Set email format to HTML
					    $mail->Subject = 'Ваши данные были обновлены!';
					    $mail->Body    = $body;
					    $mail->AltBody = strip_tags($body);

					    $mail->send();
					    echo 'Message has been sent';
					} catch (Exception $e) {
						echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
					}
					/*end email sent*/
					$password = md5($password); 
					if($dal->upd_user($user_id, $arr_t[0], $arr_t[1], $arr_t[2], $arr_t[3], $arr_t[6], $arr_t[5], $password, $arr_t[4], $arr_c[0], $arr_c[1])) { echo 'Информация пользователя успешно обновлена!'; break; }
				}
				echo 'Не удалось обновить информацию пользователя!';
				break;

			case 'update_group':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					if($dal->upd_group($user_id, $arr_t[0], $arr_t[1], $arr_c[0]))
					{ echo 'Информация группы успешно обновлена!'; break; }
				}
				else echo 'Не удалось обновить информацию группы!';
				break;

			case 'delete_user':
				if($dal->del_user($user_id))
					echo 'Пользователь успешно удален!';
				else echo 'Не удалось удалить пользователя!';
				break;

			case 'delete_group':
				if($users =  $dal->get_users_role_and_group('student', $user_id))
				{
					foreach($users as $user)
						$dal->del_user($user['id1']);
				}
				if($dal->del_group($user_id)) echo 'Группа успешно удален!';
				else echo 'Не удалось удалить группу!';
				break;
		}
		exit;
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
									<?php if($user['role']!="admin"){ ?>
										<a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary" href="mailto:<?php echo $user['email'];?>"><i class="fa fa-envelope" style="color: white"></i></a>
										<span class="pull-right">
											<a href="#profile.php?command=update_user&id=<?php echo $user['id'];?>" data-original-title="Edit this user" data-toggle="tooltip" uid="<?php echo $user_id; ?>" id="btn-update-profile-user" type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil-square-o" style="color: white"></i></a>
											<a data-original-title="Remove this user" uid="<?php echo $user_id; ?>" id="btn-delete-profile-user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="fa fa-times" style="color: white"></i></a>
										</span>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php if($user['role']!="admin") { ?>
							<div class="col col-md-3 col-lg-3 rblock" style="padding-left: 10px;">
								<div class="card panel-select">
									<div class="card-status bg-blue" style="position: inherit;"></div>
									<div class="card-header">Меню</div>
									<div class="card-body p-0">
										<nav class="profile-side-menu">
											<ul class="list-unstyled m-0">
												<li><a href="#">Информация</a></li>
												<li><a href="#album.php?id=<?php echo $user_id ?>">Альбом</a></li>
												<li><a href="#blog.php?id=<?php echo $user_id ?>">Блог</a></li>	
											</ul>
										</nav>
									</div>
								</div>
							</div>
						<?php } ?> 
					</div>

				<?php exit;

			case 'create_group':
				?>
				<div class="card panel-info">
					<div class="card-status bg-blue" style="position: inherit;"></div>
					<div class="card-header">
						<h3 class="card-title">Создание группы</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class=" col-md-12 col-lg-12 "> 
								<table class="table table-user-information">
									<tbody>
										<tr><td>Номер группы</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control group" data-mask="000000" placeholder="000000">
												</div>
											</td>
										</tr>
										<tr><td>Специальность</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control">
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра</td>
											<td>
												<div class="form-group select p-0">
													<select class="form-control custom-select">
														<?php
														$subfaculties = $dal->get_subfaculty();
														foreach($subfaculties as $subfaculty){?>
															<option value="<?php echo $subfaculty['id']?>"><?php echo $subfaculty['name']?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<span class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" id="btn-create-group">Применить</button>
						</span>
					</div>
				</div> 
			<?php exit;
				break;

			case 'create_user':
				?>
				<div class="card panel-info">
					<div class="card-status bg-blue" style="position: inherit;"></div>
					<div class="card-header">
						<h3 class="card-title">Регистрация пользователя</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class=" col-md-12 col-lg-12 "> 
								<table class="table table-user-information">
									<tbody>
										<tr><td>Фамилия</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control">
												</div>
											</td>
										</tr>
										<tr><td>Имя</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control">
												</div>
											</td>
										</tr>
										<tr><td>Отчество</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control">
												</div>
											</td>
										</tr>
										<tr><td>Деятельность</td>
											<td>
												<div class="form-group select p-0">
													<select id="groups_profile" class="form-control custom-select">
														<option selected value ="student"> Студент </option>
														<option value ="teacher"> Преподаватель </option>
														<?php if($_SESSION['role'] == "admin"){ ?>
														<option value ="staff"> Сотрудник кафедры </option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<tr class="hidden-combo-profile">
											<td>Группа:</td>
											<td>
												<div class="form-group select p-0">	
													<select id="groups_profile_number" class="form-control custom-select">
														<?php
														$groups = $dal->get_users_groups(-1);
														foreach($groups as $group){?>
															<option value="<?php echo $group['id']?>"><?php echo $group['group_number']?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td>Дата рождения</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text"  class="form-control date" data-mask="00.00.0000" placeholder="00.00.0000" autocomplete="off" maxlength="14">
												</div>
											</td>
										</tr>
										<tr>
											<td>Город</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control">
												</div>
											</td>
										</tr>
										<tr>
											<td>Email</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" placeholder="email@domain.com">
												</div>
											</td>
										</tr>
										<tr><td>Телефон</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control phone" data-mask="0(000)-000-00-00" data-mask-clearifnotmatch="true" autocomplete="off" placeholder="0(000)-000-00-00" maxlength="20">
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<span class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" id="btn-create-user">Применить</button>
						</span>
					</div>
				</div> 
			<?php exit;
				
			case 'update_user':
				$user = $dal->get_user($user_id)[0]; ?>
				<div class="card panel-info">
					<div class="card-status bg-blue" style="position: inherit;"></div>
					<div class="card-header">
						<h3 class="card-title">Редактирование информации пользователя</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class=" col-md-12 col-lg-12 "> 
								<table class="table table-user-information">
									<tbody>
										<tr><td>Фамилия</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" value="<?php echo $user['surname'];?>">
												</div>
											</td>
										</tr>
										<tr><td>Имя</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" value="<?php echo $user['name'];?>">
												</div>
											</td>
										</tr>
										<tr><td>Отчество</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" value="<?php echo $user['midname'];?>">
												</div>
											</td>
										</tr>
										<tr><td>Деятельность</td>
											<td>
												<div class="form-group select p-0">
													<select id="groups_profile" class="form-control custom-select">
														<?php if($user['role'] == 'student'){ ?>
															<option selected value ="student"> Студент </option>
															<option value ="teacher"> Преподаватель </option>
															<?php if($_SESSION['role'] == "admin"){ ?>
																<option value ="staff"> Сотрудник кафедры </option>
															<?php } ?>
														<?php }
														else if($user['role'] == "teacher") { ?>
															<option value ="student"> Студент </option>
															<option selected value ="teacher"> Преподаватель </option>
															<?php if($_SESSION['role'] == "admin"){ ?>
																<option value ="staff"> Сотрудник кафедры </option>
															<?php } ?>
														<?php }
														else { ?>
															<option value ="student"> Студент </option>
															<option value ="teacher"> Преподаватель </option>
															<option selected value ="staff"> Сотрудник кафедры </option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<?php if($user['role'] == 'student'){ ?>
										<tr class="hidden-combo-profile">
											<?php } else { ?>
											<tr class="hidden-combo-profile" style="display: none;"> <?php } ?>
											<td>Группа:</td>
											<td>
												<div class="form-group select p-0">
													<select id="groups_profile_number" class="form-control custom-select">
														<?php
														$groups = $dal->get_users_groups(-1);
														foreach($groups as $group){
															if($group['id'] == $user['id_groups']) { ?>
															<option value="<?php echo $group['id']?>" selected="true"><?php echo $group['group_number']?></option>
														<?php } else { ?> 
															<option value="<?php echo $group['id']?>"><?php echo $group['group_number']?></option> 
													<?php } 
													} ?>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td>Дата рождения</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text"  class="form-control date" data-mask="00.00.0000" placeholder="00.00.0000" autocomplete="off" maxlength="14" value="<?php $date = new DateTime($user['birth_date']); $date=$date->format('d.m.Y'); echo $date; ?>">
												</div>
											</td>
										</tr>
										<tr>
											<td>Город</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" value="<?php echo $user['city'];?>">
												</div>
											</td>
										</tr>
										<tr>
											<td>Email</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" placeholder="email@domain.com" value="<?php echo $user['email'];?>">
												</div>
											</td>
										</tr>
										<tr><td>Телефон</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control phone" data-mask="0(000)-000-00-00" data-mask-clearifnotmatch="true" autocomplete="off" placeholder="0(000)-000-00-00" maxlength="20" value="<?php echo $user['phone'];?>">
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<span class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" id="btn-update-user">Применить</button>
						</span>
					</div>
				</div> 
			<?php exit;

			case 'update_group':
				$group = $dal->get_group($user_id)[0]; ?>
				<div class="card panel-info">
					<div class="card-status bg-blue" style="position: inherit;"></div>
					<div class="card-header">
						<h3 class="card-title">Редактирование группы</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class=" col-md-12 col-lg-12 "> 
								<table class="table table-user-information">
									<tbody>
										<tr><td>Номер группы</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" data-mask="000000" placeholder="000000" value="<?php echo $group['group_number']?>">
												</div>
											</td>
										</tr>
										<tr><td>Специальность</td>
											<td>
												<div class="input-group input-group-sm">
													<input type="text" name="field-name" class="form-control" value="<?php echo $group['specialty']?>">
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра</td>
											<td>
												<div class="form-group select p-0">
													<select class="form-control custom-select">
														<?php
														$subfaculties = $dal->get_subfaculty();
														foreach($subfaculties as $subfaculty){?>
															<option value="<?php echo $subfaculty['id']?>"><?php echo $subfaculty['name']?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<span class="pull-right">
							<button type="button" class="btn btn-sm btn-primary" id="btn-update-group">Применить</button>
						</span>
					</div>
				</div> 
			<?php exit;
				break;
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