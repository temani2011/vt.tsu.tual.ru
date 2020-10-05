<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
$results = $dal->get_all_news();
if(empty($results))
{
	echo 
	'<div class="alert alert-warning" role="alert">
	Не удалось загрузить данные с сервера! Свяжитесь с администратором!
	</div>';
	exit();
}
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
	</div>
</div>
<?php foreach ($results as &$key) { ?>
	<div class="card news-card mb-4 -12">
		<!--<div class="card-status bg-blue"></div>
		<div class="card-status card-status-left bg-blue"></div>-->
		<div class="card-header">
			<h3 class="card-title"><?php echo $key["name"]; ?></h3>
			<div class="card-options"><?php $date = new DateTime($key["date"]); $key["date"] = $date->format('d.m.Y'); echo $key["date"]; ?>
			</div>
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
<?php } ?>