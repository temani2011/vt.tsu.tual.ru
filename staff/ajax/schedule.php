<?php

session_start();
require_once '../../class/schedule.php';
if(isset($_SESSION['id']))
{
	if(isset($_POST['command']) || isset($_POST['id']))
	{
		$id = $_POST['id'];
		$command = $_POST['command'];
		switch ($command) {
			case 'modal':
				$res = $dal->get_schedule($id);
				if($res) 
				{
					$new_array=array_values($res[0]);
					//print_r($new_array);
					echo json_encode($new_array);
				}
				break;

			case 'create':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					$schedule = Schedule::weekly($arr_c[0], 'teacher', $arr_c[6]);
					foreach($schedule as $pairs)
					{
						if(($pairs['week'] == $arr_c[0] || $pairs['week'] == 'both') && $pairs['day'] == $arr_c[1])
							if($pairs['pair'] == $arr_c[2]) { echo 'wrongPT'; exit(); }
					}
					$schedule = Schedule::weekly($arr_c[0], 'student', $arr_c[5]);
					foreach($schedule as $pairs)
					{
						if(($pairs['week'] == $arr_c[0] || $pairs['week'] == 'both') && $pairs['day'] == $arr_c[1])
							if($pairs['pair'] == $arr_c[2]) { echo 'wrongPG'; exit(); }
					}
					if($dal->set_new_schedule($arr_c[0], $arr_c[1], $arr_c[2], $arr_c[3], $arr_t[0], $arr_t[1], $arr_c[4], $arr_c[5], $arr_c[6])) { echo 'Новое занятие успешно добавлено!'; break; }
				}
				echo 'Не удалось добавить занятие!';
				break;

			case 'update':
				if(isset($_POST['arr_t']) && isset($_POST['arr_c']))
				{
					$arr_t = $_POST['arr_t'];
					$arr_c = $_POST['arr_c'];
					if($dal->upd_schedule($id, $arr_c[0], $arr_c[1], $arr_c[2], $arr_c[3], $arr_t[0], $arr_t[1], $arr_c[4], $arr_c[5], $arr_c[6])) { echo 'Занятие успешно обнавлено!'; break; }
				}
				echo 'Не удалось обнавить занятие!';
				break;

			case 'delete':
				if($dal->del_schedule($id)) echo 'Занятие успешно удалено!';
				else echo 'Не удалось удалить занятие';
				break;
		}
		exit();
	}

	if (isset($_GET['week']) && isset($_GET['role_id']))
	{
		$role_id = $_GET['role_id'];
		$week = $_GET['week'];
		$role = $_GET['role'];
		if (!in_array($week, ['odd', 'even', 'both'])) die('wrong week');
		$schedule = Schedule::weekly($week, $role, $role_id);
		$days = [];
		for ($i = 0; $i < count($schedule); $i++)
			$days[] = $schedule[$i]["day"];
		$days = array_unique($days);
		sort($days);
		?>
		<div class="card mt-3" id = "schedule_week">
			<div class="card-status bg-blue"></div>
			<div class="card-header">
				<div class="recent_heading" style="width: 100%">
					<?php
					$title_s = 'Расписание'; 
					if($role == 'teacher'){
						$teacher = $dal->get_user($role_id)[0];
						$title_s = $title_s . ' преподавателя ' . $teacher["surname"] . " " . $teacher["name"] . " " . $teacher["midname"];
					} 
					else { 
						$group = $dal->get_group($role_id)[0];
						$title_s = $title_s . ' группы ' . $group["group_number"];
					}?> 
					<h6 class="m-0" id = "Schedule-header-title"><?php echo $title_s ?></h6>
				</div>
				<div class="add_dialog">
					<span class="input-group-addon">
						<button type="button" data-toggle="modal" data-target="#create-schedule-modal" id="btn-add-schedule"><i class="fa fa-plus" aria-hidden="true"></i>
					</span>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col mb-1">
						<nav>
							<ul style="float: left;" class="pagination pagination-sm my-0 justify-content-end">
								<li class="page-item btnWeek" data-week="even"><a class="page-link" href="schedule.php#">ч / н</a></li>
								<li class="page-item btnWeek" data-week="odd"><a class="page-link" href="schedule.php#">н / н</a></li>
								<li class="page-item btnWeek active" data-week="both"><a class="page-link" href="schedule.php#">Все</a></li>
							</ul>
						</nav>
					</div>
				</div>
				<hr class="hr-grey">
				<div id="containerSchedule">
					<div id = 'underContainerSchedule'>
						<?php foreach($days as $day){ ?>
						<div class="container">
							<div class="row px-3"><h6><?php echo Schedule::days()[$day - 1];?></h6></div>
								<hr class="hr-grey mt-0">
								<?php foreach($schedule as $pair) { if($pair['day']==$day) { ?>
								<div class="row">
									<div class="col">
										<?php echo Schedule::pair_time($pair["pair"]);
										if ($pair['week'] == 'odd') echo ' (н/н)';
										else if($pair['week'] == 'even') echo ' (ч/н)';
										?>	
									</div>
									<div class="col"><?php echo '<b>'.$pair['short_name'].'</b><br> <small>'.$pair['corps'].'-'.$pair['auditory'].'</small>'; ?>
									</div>
									<div class="col"><?php echo $pair['group_number']; ?></div>
									<div class="col">
										<div id="btn-update-schedule" class="btn-update-delete" schedid="<?php echo $pair['id1'];?>"><i class="fa fa-pencil" aria-hidden="true" style="padding: 12px"></i></div>
										<div id="btn-delete-schedule" class="btn-update-delete" schedid="<?php echo $pair['id1'];?>"><i class="fa fa-times" aria-hidden="true" style="padding: 12px"></i></div>
									</div>
								</div>
							<?php } } ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" id="create-schedule-modal" tabindex="-1" role="dialog" aria-labelledby="create-schedule-modal-modalLabel" aria-hidden="true">
			<div class="modal-dialog custom">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="create-schedule-modalLabel">Добавление занятия</h5>
						<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
							&times;
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group select p-0">
							<span>Неделя</span>
							<select id="modal_schedule_week" class="form-control modal-custom-select">
								<option selected value ="both"> Обе </option>
								<option value ="even"> ч/н </option>
								<option value ="odd"> н/н </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>День</span>
							<select id="modal_schedule_day" class="form-control modal-custom-select">
								<option selected value ="1"> Понедельник </option>
								<option value ="2"> Вторник </option>
								<option value ="3"> Среда </option>
								<option value ="4"> Четверг </option>
								<option value ="5"> Пятница </option>
								<option value ="6"> Суббота </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Занятие по счету</span>
							<select id="modal_schedule_pair" class="form-control modal-custom-select">
								<option selected value ="1"> 1 </option>
								<option value ="2"> 2 </option>
								<option value ="3"> 3 </option>
								<option value ="4"> 4 </option>
								<option value ="5"> 5 </option>
								<option value ="6"> 6 </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Тип занятия</span>
							<select id="modal_schedule_type" class="form-control modal-custom-select">
								<option selected value ="lecture"> Лекция </option>
								<option value ="practice"> Практика </option>
								<option value ="lab"> Лабараторная </option>
								<option value ="seminar"> Семинар </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Корпус</span>
							<div class="input-group input-group">
								<input type="text" id = "modal_schedule_corp" class="form-control">
							</div>
						</div>
						<div class="form-group select p-0">
							<span>Аудитория</span>
							<div class="input-group input-group">
								<input type="text" id = "modal_schedule_audit" class="form-control">
							</div>
						</div>
						<div class="form-group select p-0">
							<span>Предмет</span>
							<select id="modal_schedule_subjects" class="form-control modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$subjects = $dal->get_subjects();
								foreach($subjects as $subject){?>
									<option value="<?php echo $subject['id']?>"><?php echo $subject['name']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Группа</span>
							<select id="modal_schedule_groups" class="form-control modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$groups = $dal->get_users_groups(-1);
								foreach($groups as $group){?>
									<option value="<?php echo $group['id']?>"><?php echo $group['group_number']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Преподаватель</span>
							<select id="modal_schedule_teachers" class="form-control modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$teachers = $dal->get_users_role('teacher');
								foreach($teachers as $teacher){?>
								<option value="<?php echo $teacher['id1']?>"><?php echo $teacher["surname"] . " " . $teacher["name"] . " " . $teacher["midname"] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btn-create-schedule">Применить</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" id="update-schedule-modal" tabindex="-1" role="dialog" aria-labelledby="update-schedule-modal-modalLabel" aria-hidden="true">
			<div class="modal-dialog custom">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="update-schedule-modalLabel">Изменение занятия</h5>
						<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
							&times;
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group select p-0">
							<span>Неделя</span>
							<select id="modal_schedule_week" class="form-control update-modal-custom-select">
								<option selected value ="both"> Обе </option>
								<option value ="even"> ч/н </option>
								<option value ="odd"> н/н </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>День</span>
							<select id="modal_schedule_day" class="form-control update-modal-custom-select">
								<option selected value ="1"> Понедельник </option>
								<option value ="2"> Вторник </option>
								<option value ="3"> Среда </option>
								<option value ="4"> Четверг </option>
								<option value ="5"> Пятница </option>
								<option value ="6"> Суббота </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Занятие по счету</span>
							<select id="modal_schedule_pair" class="form-control update-modal-custom-select">
								<option selected value ="1"> 1 </option>
								<option value ="2"> 2 </option>
								<option value ="3"> 3 </option>
								<option value ="4"> 4 </option>
								<option value ="5"> 5 </option>
								<option value ="6"> 6 </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Тип занятия</span>
							<select id="modal_schedule_type" class="form-control update-modal-custom-select">
								<option selected value ="lecture"> Лекция </option>
								<option value ="practice"> Практика </option>
								<option value ="lab"> Лабараторная </option>
								<option value ="seminar"> Семинар </option>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Корпус</span>
							<div class="input-group input-group">
								<input type="text" id = "modal_schedule_corp" class="form-control update-modal-custom-input">
							</div>
						</div>
						<div class="form-group select p-0">
							<span>Аудитория</span>
							<div class="input-group input-group">
								<input type="text" id = "modal_schedule_audit" class="form-control update-modal-custom-input">
							</div>
						</div>
						<div class="form-group select p-0">
							<span>Предмет</span>
							<select id="modal_schedule_subjects" class="form-control update-modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$subjects = $dal->get_subjects();
								foreach($subjects as $subject){?>
									<option value="<?php echo $subject['id']?>"><?php echo $subject['name']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Группа</span>
							<select id="modal_schedule_groups" class="form-control update-modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$groups = $dal->get_users_groups(-1);
								foreach($groups as $group){?>
									<option value="<?php echo $group['id']?>"><?php echo $group['group_number']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group select p-0">
							<span>Преподаватель</span>
							<select id="modal_schedule_teachers" class="form-control update-modal-custom-select">
								<option selected value ="lecture"> -- </option>
								<?php
								$teachers = $dal->get_users_role('teacher');
								foreach($teachers as $teacher){?>
								<option value="<?php echo $teacher['id1']?>"><?php echo $teacher["surname"] . " " . $teacher["name"] . " " . $teacher["midname"] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" schedid="" id="btn-accept-update-schedule">Применить</button>
					</div>
				</div>
			</div>
		</div>
	<?php
	exit();
	}

	$today = Schedule::date_today(); 
	if(strpos($today, '(н/н)') !== false) $week = 'odd'; 
	else if(strpos($today, '(ч/н)') !== false) $week = 'even';
	
	?>
	<div class="card">
		<div class="card-status bg-blue"></div>
		<h6 class="card-header"><?php echo Schedule::date_today(); ?></h6>
		<div class="card-body">
			<span>
				<h6>Расписание групп</h6>
				<select id="schedule_groups" class="form-control custom-select" weekValue="<?php echo $week ?>">
					<option  selected data-hidden="true"> -- Выбрать -- </option>
					<?php
					$groups = $dal->get_users_groups(-1);
					foreach($groups as $group){ ?>
						<option value="<?php echo $group['id']?>"><?php echo $group['group_number']?></option>
					<?php } ?>
				</select>
			</span>
			<hr class="hr-grey">
			<span>
				<h6>Расписание преподавателей</h6>
				<select id="schedule_teachers" class="form-control custom-select" weekValue="<?php echo $week ?>">
					<option  selected data-hidden="true"> -- Выбрать -- </option>
					<?php
					$teachers = $dal->get_users_role('teacher');
					foreach($teachers as $teacher){?>
						<option value="<?php echo $teacher['id1']?>"><?php echo $teacher["surname"] . " " . $teacher["name"] . " " . $teacher["midname"] ?></option>
					<?php } ?>
				</select>
			</span>
		</div>
	</div>
	
	<?php
}
else
{	
  $err ='
	<div class="alert alert-warning" role="alert">
		У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
	</div>';
	echo $err;
}
?>