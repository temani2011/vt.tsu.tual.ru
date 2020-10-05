<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
if($_SESSION['id'])
{
	$groups = $dal->get_all_groups(); ?>
	<div class="card-status bg-blue" style="position: inherit;"></div>
	<table class="table table-condensed" id="groups_table">
		<thead class="thead">
			<tr>
				<th scope="col">Группа</th>
				<th scope="col">Факультет</th>
				<th scope="col">Специальность</th>
				<th style="width: 15%"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$counter = 0;
			foreach ($groups as $group) 
			{ ?>   
				<th scope="row"><a class="group" href="#journal.php?group=<?php echo $group['group_number'];?>"><?php echo $group['group_number'];?></a></th>
				<td><?php echo $group['name'];?></td>
				<td><?php echo $group['specialty'];?></td>
				<td>
					<a id="btn-update-journal-group" class="btn-update-delete" href="#profile.php?command=update_group&id=<?php echo $group['id1'];?>" gid="<?php echo $group['id1'];?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
					<div id="btn-delete-journal-group" class="btn-update-delete" gid="<?php echo $group['group_number'];?>"><i class="fa fa-times" aria-hidden="true"></i></div>
				</td>
			</tr>
			<?php } ?> 
		</tbody>
	</table>
<?php 
	if(isset($_GET['group']))
	{ 
		$group_number = $_GET['group']; 
		$users = $dal->get_users_role_and_group('student', $group_number);
		if(!empty($users)) { ?>
			<div class="card-status bg-blue" style="position: inherit;"></div>
			<table class="table table-condensed" id="students_table">
				<thead class="thead">
					<tr>
						<th scope="col">Студент</th>
						<th scope="col">email</th>
						<th scope="col">Телефон</th>
						<th style="width: 15%"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($users as $user) 
					{ ?>
						<td><a href="#profile.php?command=select&id=<?php echo $user['id1'];?>"><?php echo ($user["surname"] ." " . mb_substr($user["name"], 0,1,"UTF-8") .". " . mb_substr($user["midname"], 0,1,"UTF-8"). ".");?></a></td>
						<td><a href="mailto:<?php echo $user['email']?>" ><?php echo $user['email'];?></a></td>
						<td><?php echo $user['phone'];?></td>
						<td>
							<a id="btn-update-journal-user" class="btn-update-delete" uid="<?php echo $user['id1'];?>" href="#profile.php?command=update_user&id=<?php echo $user['id1'];?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
							<div id="btn-delete-journal-user" class="btn-update-delete" uid="<?php echo $user['id1'];?>"><i class="fa fa-times" aria-hidden="true"></i></div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php }
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