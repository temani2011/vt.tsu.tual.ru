<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();

if(isset($_GET['id']) || isset($_POST['id']))
{
	$id = $_GET['id'];
	$user = $dal->get_user($id)[0];
	$blog = $dal->get_blog_posts($id);
	if(isset($_POST['pid']) || isset($_POST['command']))
	{
		$pid = $_POST['pid'];
		$command = $_POST['command'];
		switch ($command) {
			case 'modal':
			$post = $dal->get_blog_post($pid);
			if($post) echo json_encode($post[0]);
			break;

			case 'create':
			if($dal->set_blog_post($_POST['nname'], $_POST['txt'], $_POST['id']))
				echo "Публикация успешно создана!";
			else echo "Не удалось создать публикацию!";
			break;

			case 'update':
			if($dal->upd_blog_post($pid, $_POST['nname'], $_POST['txt']))
				echo "Публикация успешно обновлена!";
			else echo "Не удалось обновить публикацию!";
			break;

			case 'delete':
			if($dal->del_blog_post($pid))
				echo "Публикация успешно удалена!";
			else echo "Не удалось публикацию!";
			break;
		}
		exit();
	}
 ?>
	<div class="row mx-auto" style="justify-content: center;">
		<div class="col-12 col-md-3 col-lg-2 rblock" style="padding-left: 10px;">
			<div class="card mb-3 lblock" style=" border-bottom: 0px;">
				<div class="card-status bg-blue"></div>
				<div class="card-header m-3 p-3">
					<a href="#profile.php?command=select&id=<?php echo $id ?>">
						<?php if(!$user['img']) { ?>
							<img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
						<?php } else { ?>
							<img alt="User Pic" src="<?php echo $user['img'];?>"  width="300" height="auto" class="rounded-circle">
						<?php } ?>
					</a>
				</div>
				<div class="card-body pt-0">Количество постов: <?php echo count($blog) ?></div>
			</div>
		</div>
		<div class="col-12 col-md-9 col-lg-9 rblock" style="padding-left: 10px;">
			<div class="card mb-4" style=" border-bottom: 0px;">
				<div class="card-status bg-blue"></div>
				<div class="card-header">
					<div class="recent_heading">
						<h4>Блог</h4>
					</div>
					<div class="srch_bar">
						<div class="stylish-input-group">
							<span class="search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
							<input type="text" class="search-bar" id="blog-search-bar" placeholder="Поиск" style="
							padding-left: 25px;">
						</div>
					</div>
					<?php if($_SESSION['id'] == $id){ ?>
					<div class="add_dialog">
						<span class="input-group-addon">
							<button type="button" data-toggle="modal" data-target="#create-blog-modal" id="btn-add-blog"><i class="fa fa-plus" aria-hidden="true"></i>
						</span>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php foreach ($blog as $post) { ?>
				<div class="card blog-card mb-4 -12">
					<div class="card-header">
						<h3 class="card-title"><?php echo $post["name"]; ?></h3>
						<div class="card-options"><?php $date = new DateTime($post["date"]); $post["date"] = $date->format('d.m.Y'); echo $post["date"]; ?>
					</div>
					<?php if($_SESSION['id'] == $id){ ?>
					<div class="card-options m-0 pl-2">
						<div class="settings blog" pid = "<?php echo $post['id'] ?>">
							<span class="input-group-addon">
								<button type="button" data-toggle="dropdown" id="btn-settings-blog"><i class="fa fa-cog" aria-hidden="true"></i></button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="#" id="dd-blog-update">Редактировать</a> 
									<a class="dropdown-item" href="#" id="dd-blog-delete">Удалить</a>
								</div>
							</span>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="card-body">
					<?php echo $post["text"]; ?><br><br>
				</div>
				<div class="card-footer"><textarea style="width: 100%" placeholder="Введите комментарий"></textarea></div>
			</div>
		<?php } ?>
		</div>
	</div>
	<div class="modal" id="update-blog-modal" tabindex="-1" role="dialog" aria-labelledby="update-blog-modal-modalLabel" aria-hidden="true">
		<div class="modal-dialog blog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="update-blog-modalLabel">Редактирование публикации</h5>
					<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
						&times;
					</button>
				</div>
				<div class="modal-header" id="blog-name">
					<div class="stylish-input-group-modal">
						<p style="display: inline;">Название публикации: </p>
						<input type="text" class="dialog-name-text" id="blog-name-text-input-update" value="<?php echo $blog[0]['name'];?>">
					</div>
				</div>
				<div class="modal-body" style="height: 300px;">
					<textarea class="form-control" id = "text-modal-update-blog" placeholder="Введите текст" value="<?php echo $blog[0]['text']; ?>" style="margin-top: 0px;margin-bottom: 0px;height: 200px;"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" nid="" id="btn-update-blog">Применить</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="create-blog-modal" tabindex="-1" role="dialog" aria-labelledby="create-blog-modal-modalLabel" aria-hidden="true">
		<div class="modal-dialog blog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="create-blog-modalLabel">Создание публикации</h5>
					<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
						&times;
					</button>
				</div>
				<div class="modal-header" id="blog-name">
					<div class="stylish-input-group-modal">
						<p style="display: inline;">Название публикации: </p>
						<input type="text" class="dialog-name-text" id="blog-name-text-input-create">
					</div>
				</div>
				<div class="modal-body" style="height: 300px;">
					<textarea class="form-control" id = "text-modal-create-blog" placeholder="Введите текст" style="margin-top: 0px;margin-bottom: 0px;height: 200px;"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" nid="" id="btn-create-blog">Применить</button>
				</div>
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
}
?>