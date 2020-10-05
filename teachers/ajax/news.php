<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
if(isset($_POST['nid']) || isset($_POST['command']))
{
	$nid = $_POST['nid'];
	$command = $_POST['command'];
	switch ($command) {
		case 'modal':
			$news = $dal->get_news($nid);
			if($news) echo json_encode($news[0]);
			break;
		
		case 'create':
			if($dal->set_news($_POST['nname'], $_POST['txt'], $_SESSION['id']))
				echo "Новость успешно создана!";
			else echo "Не удалось создать новость!";
			break;

		case 'update':
			if($dal->upd_news($nid, $_POST['nname'], $_POST['txt']))
				echo "Новость успешно обновлена!";
			else echo "Не удалось обновить новость!";
			break;
		
		case 'delete':
			if($dal->del_news($nid))
				echo "Новость успешно удалена!";
			else echo "Не удалось удалить новость!";
			break;
	}
	exit();
}
else
{
	$results = $dal->get_all_news();
?>
<div class="card mb-4" style=" border-bottom: 0px;">
			<div class="card-status bg-blue"></div>
	<div class="card-header">
		<div class="recent_heading">
			<h4>Новости</h4>
		</div>
		<div class="srch_bar">
			<div class="stylish-input-group">
				<span class="search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
				<input type="text" class="search-bar" id="news-search-bar" placeholder="Поиск" style="
				padding-left: 25px;">
			</div>
		</div>
		<?php if(isset($_SESSION['id'])) { ?>
		<div class="add_dialog">
			<span class="input-group-addon">
				<button type="button" data-toggle="modal" data-target="#create-news-modal" id="btn-add-news"><i class="fa fa-plus" aria-hidden="true"></i>
			</span>
		</div>
		<?php } ?>
	</div>
</div>
<?php	if(!empty($results)) {
	foreach ($results as &$key) { ?>
	<div class="card news-card mb-4 -12">
		<!--<div class="card-status bg-blue"></div>
		<div class="card-status card-status-left bg-blue"></div>-->
		<div class="card-header">
			<h3 class="card-title"><?php echo $key["name"]; ?></h3>
			<div class="card-options"><?php $date = new DateTime($key["date"]); $key["date"] = $date->format('d.m.Y'); echo $key["date"]; ?>
			</div>
			<?php if(isset($_SESSION['id'])) { ?>
			<div class="card-options m-0 pl-2">
				<div class="settings news" nid = "<?php echo $key['id'] ?>">
					<span class="input-group-addon">
						<button type="button" data-toggle="dropdown" id="btn-settings-news"><i class="fa fa-cog" aria-hidden="true"></i></button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<a class="dropdown-item" href="#" id="dd-news-update">Редактировать</a> 
							<a class="dropdown-item" href="#" id="dd-news-delete">Удалить</a>
						</div>
					</span>
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="card-body">
			<?php echo $key["text"]; ?><br><br>
			<?php if($key['imgs']) 
			{ 
				$imgs = explode(' ', $key['imgs']); ?>
				<div class="row" style="text-align: center;">
					<?php foreach ($imgs as $img) { ?>
						<div class="col">
							<img src="<?php echo $img;?>" href="#" class="rounded mx-auto"/>
						</div>
					<?php } ?>
				</div> 
		<?php } ?> 
		</div>
	</div>
<?php } } else { ?> 
	<h5 class="text-secondary text-center m-3">
		Нет новостей
	</h5>
<?php } ?>
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
<?php } ?>