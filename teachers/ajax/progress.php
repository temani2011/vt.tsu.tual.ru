<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
require_once '../../class/schedule.php';
$dal = new DAL();
if(isset($_SESSION['id']))
{
	if(isset($_POST['command']) || (isset($_POST['sid']) && isset($_POST['uid']) && isset($_POST['command'])))
	{
		$uid = $_POST['uid'];
		$sid = $_POST['sid'];
		$command = $_POST['command'];
		switch ($command) {
			case 'load_combo':
				if(isset($_POST['group']))
					$sub = $dal->get_subjects_for_group($_POST['group']);
				echo json_encode($sub);
				break;

			case 'create':
				$score_arr = $_POST['score_arr'];
				$attend_arr = $_POST['attend_arr'];
				for($i = 0; $i < count($uid); $i++) {
					if(!$dal->set_attendance($sid, $uid[$i], $score_arr[$i], $attend_arr[$i])){ echo 'Не удалось добавить данные'; exit();};
				}
				echo 'Данные успешно добавлены';
				break;

			case 'update':
				$score_arr = $_POST['score_arr'];
				$attend_arr = $_POST['attend_arr'];
				for($i = 0; $i < count($uid); $i++) {
					if(!$dal->upd_attendance($sid, $uid[$i], $score_arr[$i], $attend_arr[$i])){ echo 'Не удалось обновить данные'; exit();}
				}
				echo 'Данные успешно обновлены';
				break;
		}
		exit();
	}

	if(isset($_GET['group']) && isset($_GET['sid']))
	{
		$count = 0;
		$sid = $_GET['sid'];
		$group = $_GET['group'];
		$users = $dal->get_users_role_and_group('student', $group);
		?>

		<div class="card">
			<div class="card-status bg-blue"></div>
			<div class="card-header"> Группа <?php echo $group;?></div>
			<div class="card-body">
				<table style="width: 100%;">
					<tr style="text-align: center;">
						<th width="5%" style="text-align: left">#</th>
						<th style="text-align: left;">ФИО</th>
						<th>Посещаемость</th>
						<th>Успеваемость</th>
						<?php if(!isset($_GET['global'])){ ?>
						<th>Посещение</th>
						<th>Оценка</th>
						<?php } ?>
					</tr>
					<?php foreach($users as $user) { $count++; $att = $dal->get_attendance($sid, $user['id1'])[0]; if($att) $exist = true; $sum_att = $dal->get_sum_attendance($sid, $user['id1'])[0]; ?>
					<tr name="progress-user" uid="<?php echo $user['id1']; ?>">
						<td><?php echo $count; ?></td>
						<td><?php echo $user["surname"] . " " . $user["name"] . " " . $user["midname"] ?></td>
						<td style="text-align: center;"><?php echo $a = intval($sum_att['attend_sum']); ?>/<?php echo $b = intval($sum_att['hours'] * 0.5); ?> (<?php echo round($a/$b,2); ?>%)</td>
						<td style="text-align: center;"><?php if(intval($sum_att['score_count']) == 0) echo '0.0'; else echo round((intval($sum_att['score_sum'])/intval($sum_att['score_count'])),1); ?></td>
						<?php if(!isset($_GET['global'])){ 
								if($exist) { ?>
							<td style="text-align: center;">
								<?php if($att['attend']) { ?>
									<input type="checkbox" id='attendCheckBox' class="modal-user-checkbox progress-pair" checked ="true">
								<?php } else { ?>
									<input type="checkbox" id='attendCheckBox' class="modal-user-checkbox progress-pair">
								<?php } ?>
							</td>
							<td>
								<div class="form-group select p-0">
									<select id="scoreSelect" class="form-control custom-select">
										<option selected value =""> - </option>
										<?php for($i = 1; $i <= 5; $i++) {
											if($i == $att['score']) { ?>
												<option selected value ="<?php echo $i?>"> <?php echo $i?> </option>
											<?php } else { ?> 	
												<option value ="<?php echo $i?>"> <?php echo $i?> </option>
											<?php } } ?>
									</select>
								</div>
							</td>
						<?php } else { ?>
							<td style="text-align: center;">
								<input type="checkbox" id='attendCheckBox' class="modal-user-checkbox progress-pair">
							</td>
							<td>
								<div class="form-group select p-0">
									<select id="scoreSelect" class="form-control custom-select">
										<option selected value =""> - </option>
										<?php for($i = 1; $i <= 5; $i++) { ?>
											<option value ="<?php echo $i?>"> <?php echo $i?> </option>
										<?php } ?>
									</select>
								</div>
							</td>
						<?php } ?>
					</tr>
					<?php } } ?>
				</table>
			</div>
			<?php if(!isset($_GET['global'])){ ?>
			<div class="card-footer">
				<span class="pull-right">
					<button type="button" class="btn btn-sm btn-primary" exist='<?php echo $exist ?>' id="btn-progress-accept">Применить</button>
				</span>
			</div>
		<?php } ?>
		</div>
		<?php
		exit();
	}
	$schedule = Schedule::today($_SESSION['role'], $_SESSION['id']);
?>
<div class="card mb-3">
	<div class="card-status bg-blue"></div>
	<h6 class="card-header">Успеваемость</h6>
	<div class="card-body">
		<span>
			<h6>Группа</h6>
			<select id="progress_groups" class="form-control custom-select" style="width: auto;">
				<option  selected data-hidden="true"> -- Выбрать -- </option>
				<?php
				$groups = $dal->get_users_groups(-1);
				foreach($groups as $group){ ?>
					<option value="<?php echo $group['group_number']?>"><?php echo $group['group_number']?></option>
				<?php } ?>
			</select>
		</span>
		<hr class="hr-grey">
		<span>
			<h6>Предмет</h6>
			<select id="progress_subjects" class="form-control custom-select" style="width: auto;">
				<option value = "all" selected> -- Выбрать -- </option>
				<?php
				$subjects = $dal->get_subjects();
				foreach($subjects as $subject){?>
					<option value="<?php echo $subject['id1']?>"><?php echo $subject["name"]?></option>
				<?php } ?>
			</select>
		</span>
		<div class="mt-3">
			<button type="button" class="btn btn-sm btn-primary" id="btn-progress-select">Применить</button>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-status bg-blue"></div>
	<h6 class="card-header"> Проставление успеваемости: <?php echo Schedule::date_today(); ?> </h6>
	<div class="card-body">
		<table style="width: 100%">
			<?php if($schedule) { ?>
			<!--<tr><td colspan="2" class="time"><i><u><?php echo Schedule::date_today(); ?></u></i></td></tr>-->
			<?php foreach($schedule as $pair) { ?>
			<tr id="progress-pair" onclick="location.href='#progress.php?group=<?php echo $pair['group_number']?>&sid=<?php echo $pair['id2']?>'">
				<td class="time"><?php echo Schedule::pair_time($pair["pair"]); ?></td>	
				<td style="padding: 10px 0px 10px 0px; vertical-align: bottom;">
					<div>
						<span class="disc"><?php echo $pair['name']; ?> (<?php echo Schedule::pair_type($pair['type']); ?>)</span>,
						<span class="aud">
							ауд. <a href="http://schedule.tsu.tula.ru/?aud=Гл.-418" class="glink"><?php echo $pair['corps'].'-'.$pair['auditory']; ?></a>
						</span>
						<div>
							<div class="teac">
								<a href="http://schedule.tsu.tula.ru/?teacher=8294" class="glink"><?php echo $_SESSION["surname"] . " " . $_SESSION["name"] . " " . $_SESSION["midname"] ?></a>
							</div>
						</div>
					</div>
				</td>
				<td style="padding-left: 16px;">
					<a href="#"><?php echo $pair['group_number']; ?></a>
				</td>
			</tr>
			</a>
			<?php } } else { ?>
				<h5 class="text-secondary text-center m-3">Нет занятий</h5>
			<?php } ?>
		</table>
	</div>
</div>
<?php
}
else
{
	$err ='<div class="alert alert-warning" role="alert">
				У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
		   </div>';
	echo $err;
} ?>
<div class="modal" id="update-news-modal" tabindex="-1" role="dialog" aria-labelledby="update-news-modal-modalLabel" aria-hidden="true">
	<div class="modal-dialog news" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-news-modalLabel">Редактирование новости</h5>
				<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
					&times;
				</button>
			</div>
			<div class="modal-header" id="news-name">
				<div class="stylish-input-group-modal">
					<p style="display: inline;">Название новости: </p>
					<input type="text" class="dialog-name-text" id="news-name-text-input-update" value="<?php echo $news[0]['name'];?>">
				</div>
			</div>
			<div class="modal-body" style="height: 300px;">
				<textarea class="form-control" id = "text-modal-update-news" placeholder="Введите текст" value="<?php echo $news[0]['text']; ?>" style="margin-top: 0px;margin-bottom: 0px;height: 200px;"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" nid="" id="btn-update-news">Применить</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="create-news-modal" tabindex="-1" role="dialog" aria-labelledby="create-news-modal-modalLabel" aria-hidden="true">
	<div class="modal-dialog news" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="create-news-modalLabel">Создание новости</h5>
				<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
					&times;
				</button>
			</div>
			<div class="modal-header" id="news-name">
				<div class="stylish-input-group-modal">
					<p style="display: inline;">Название новости: </p>
					<input type="text" class="dialog-name-text" id="news-name-text-input-create">
				</div>
			</div>
			<div class="modal-body" style="height: 300px;">
				<textarea class="form-control" id = "text-modal-create-news" placeholder="Введите текст" style="margin-top: 0px;margin-bottom: 0px;height: 200px;"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" nid="" id="btn-create-news">Применить</button>
			</div>
		</div>
	</div>
</div>