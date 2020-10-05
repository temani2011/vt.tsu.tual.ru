<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
require_once '../../class/schedule.php';
$dal = new DAL();
if(isset($_SESSION['id']))
{
	if(isset($_GET['group']) && isset($_GET['sid']))
	{
		$count = 0;
		$sid = $_GET['sid'];
		$group = $_GET['group'];
		$users = $dal->get_users_role_and_group('student', $group);
		?>

		<div class="card" id = "progress_sdf">
			<div class="card-status bg-blue"></div>
			<div class="card-header"> Группа <?php echo $group;?></div>
			<div class="card-body">
				<table style="width: 100%;">
					<tr style="text-align: center;">
						<th width="5%" style="text-align: left">#</th>
						<th style="text-align: left;">ФИО</th>
						<th>Посещаемость</th>
						<th>Успеваемость</th>
					</tr>
					<?php foreach($users as $user) { $count++; $sum_att = $dal->get_sum_attendance($sid, $user['id1'])[0]; if($user['id1'] == $_SESSION['id']) { ?>
					<tr name="progress-user" uid="<?php echo $user['id1']; ?>">
						<td><?php echo $count; ?></td>
						<td><?php echo $user["surname"] . " " . $user["name"] . " " . $user["midname"] ?></td>
						<td style="text-align: center;"><?php echo $a = intval($sum_att['attend_sum']); ?>/<?php echo $b = intval($sum_att['hours'] * 0.5); ?> (<?php echo round($a/$b,2); ?>%)</td>
						<td style="text-align: center;"><?php if(intval($sum_att['score_count']) == 0) echo '0.0'; else echo round((intval($sum_att['score_sum'])/intval($sum_att['score_count'])),1); ?></td>
					</tr>
					<?php } } ?>
				</table>
			</div>
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
			<?php $groupp = $dal->get_group($_SESSION['id_groups'])[0]['group_number']; ?>
			<h6>Предмет</h6>
			<select id="progress_subjects" class="form-control custom-select" style="width: auto;">
				<option value = "all" selected> -- Выбрать -- </option>
				<?php 
				$subjects = $dal->get_subjects_for_group($groupp);
				foreach($subjects as $subject){?>
					<option value="<?php echo $subject['id1']?>"><?php echo $subject["name"]?></option>
				<?php } ?>
			</select>
		</span>
		<div class="mt-3">
			<button type="button" class="btn btn-sm btn-primary" gid = "<?php echo $groupp; ?>" id="btn-progress-select">Применить</button>
		</div>
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