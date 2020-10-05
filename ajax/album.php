<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
if (isset($_FILES['file']['tmp_name']))
{
	$base_folder = $_SERVER['DOCUMENT_ROOT'];
	$new_file = $base_folder . '/img/'.$_SESSION['id'].'/' .  $_FILES['file']['name'];
	move_uploaded_file($_FILES['file']['tmp_name'], $new_file);
	if($dal->set_new_album_img($_SESSION['id'], '/img/' .$_SESSION['id'].'/' . $_FILES['file']['name'])) echo 'Файл успешно загружен!';
	exit();
}
if(isset($_GET['id']) || isset($_POST['id']))
{
	$description = "";
	$date = "";
	$id = $_GET['id'];
	$user = $dal->get_user($id)[0];
	$imgs = $dal->get_imgs($id);

	if(isset($_POST['img_id']) && isset($_POST['user_id']) && isset($_POST['command']))
	{
		$command = $_POST['command'];
		$uid = $_POST['user_id'];
		$iid = $_POST['img_id'];
		switch ($command) {
			case 'like':
				$check = true;
				$count = count($dal->get_like($iid, $uid));
				if($count > 0){
					$check = false;
					$dal->del_like($iid,$uid);
					$count--;
				}
				else { $dal->set_like($iid,$uid); $count++; }
				$data = [$check, $count];
				echo json_encode($data);
				break;
			
			case 'upload':

			case 'comment':
				$text = $_POST['txt'];
				$t = $dal->set_comment($iid, $uid, $text);
			    $comments = $dal->get_comments($iid);
			    ?>
				<div class="card-body msg_history p-0" id="msg_history" style="height: 300px">
					<div class="row msg-row" id="messages">
						<table id="chats" class="table table-striped">
							<tbody>
								<?php if(count($comments) > 0) { foreach($comments as $comment) { ?>
									<tr>
										<td valign="top">
											<div style="display: inline;">
												<strong><small><?php echo ($comment['surname'] . " " . $comment['name'] . " " . $comment['midname']);?></small></strong>
												<div><small><?php echo $comment['text'] ?></small></div>
											</div>
										</td>
										<td align="right" valign="top">
											<small>
												<?php $date = new DateTime($comment['date_c']); $date=$date->format('H:i d.m.Y'); echo $date; ?>
											</small>
										</td>
									</tr>
								<?php } } else { ?>
									<div>Нет комментариев</div>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div style = "position: absolute;width: 100%; margin-bottom: 15px; padding-right: 30px; bottom: 0px">
				<?php
				break;
		}
	 	exit();
	}
	if(isset($_GET['img_id']))
	{
		$check_lk = false;
		$img_id = $_GET['img_id'];
	 	$likes = $dal->get_likes($img_id);
		$comments = $dal->get_comments($img_id);
		foreach($imgs as $img)
			if($img['id'] == $img_id)
			{
				$description = $img['description'];
				$date = $img['date'];
			}
		foreach($likes as $like)
			if($like['id_user'] == $id)
			{
				$check_lk = true;
			} 
			?>
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	            <div class="modal-dialog modal-lg" role="document" style="padding: 1.75rem; max-width: fit-content;">
	                <div class="modal-content" style="border-width: 0px">
	                    <div class="row" style="margin-left: 0px; margin-right: 0px;">
	                        <div class="col-md-8 p-0 d-flex align-items-center" style="background-color: black" >
	                            <div id="myCarousel" class="carousel" data-interval="false" data-ride="false" style="margin: auto;">
	                                <div class="carousel-inner" role="listbox">
	                                	<?php $count=0; foreach($imgs as $img){ $count++; $size = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img['path']);?>
	                                    <div class="carousel-item" id="carousel-imgs">
	                                    	<img id="<?php echo $img['id']?>" class="d-block w-100" src="<?php echo $img['path']?>" alt="<?php echo $count?>">
	                                    </div>
	                                	<?php } ?>
	                                </div>
	                                <!-- Left and right controls -->
	                                <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
	                                    <span class="carousel-control-prev-icon	" aria-hidden="true"></span>
	                                    <span class="sr-only">Previous</span>
	                                </a>
	                                <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
	                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
	                                    <span class="sr-only">Next</span>
	                                </a>
	                            </div>
	                        </div>
	                        <div class="col-md-4 p-0">
	                            <div class="modal-body inline" style="height: 100%; position: relative;">
	                                <div class="row">
	                                    <div class="col-md-3">
	                                        <img src="<?php echo $user['img'] ?>" class="img-circle p-0">
	                                    </div>
	                                    <div class="col-md-9">
	                                    	<div><small><?php echo $description ?></small></div>
	                                    	<div><small><?php $date = new DateTime($key["date"]); $key["date"] = $date->format('d.m.Y'); echo $key["date"]; ?></small></div>
	                                    	<?php if($check_lk) { ?>
	                                        <button id="btn-like" mid="<?php echo $id ?>" iid="<?php echo $img_id ?>" uid="<?php echo $_SESSION['id']?>" type="button" style="color: red"><i class="fa fa-heart" aria-hidden="true"></i></button><?php echo count($likes)?>
	                                    	<?php } else { ?>
	                                    	<button id="btn-like" mid="<?php echo $id ?>" iid="<?php echo $img_id ?>" uid="<?php echo $_SESSION['id']?>" type="button" style="color: #707070"><i class="fa fa-heart" aria-hidden="true"></i></button>
	                                    	<div id="likes_count" style="display: inline;"><?php echo count($likes)?></div>	
	                                    	<?php } ?>
	                                    </div>
	                                </div>
	                            	<div class="card-body msg_history p-0" id="msg_history" style="height: 300px">
								        <div class="row msg-row" id="messages">
								          <table id="chats" class="table table-striped">
								            <tbody>
								            <?php if(count($comments) > 0) { foreach($comments as $comment) { ?>
								              <tr>
								                <td valign="top">
								                  <div style="display: inline;">
								                    <strong><?php echo ($comment['surname'] . " " . $comment['name'] . " " . $comment['midname']);?></strong>
								                    <div><?php echo $comment['text'] ?></div>
								                  </div>
								              	</td>
								              	<td align="right" valign="top">
								              		<small>
								                  		<?php $date = new DateTime($comment['date_c']); $date=$date->format('H:i d.m.Y'); echo $date; ?>
								                  	</small>
								                </td>
								              </tr>
								            <?php } } else { ?>
								            	<div>Нет комментариев</div>
								            <?php } ?>
								            </tbody>
								          </table>
							            </div>
							        </div style = "position: absolute;width: 100%; margin-bottom: 15px; padding-right: 30px; bottom: 0px">
							        <div>
		                                <input id="txt-comment" placeholder="Comment" type="text" style="height:100px" class="form-control" />
		                                <button id="btn-add-comment" mid="<?php echo $id ?>" iid="<?php echo $img_id ?>" uid="<?php echo $_SESSION['id']?>" class="btn btn-sm btn-primary pull-right">Save</button>
		                            </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
			<?php
			exit();
	}
	?>
	<div class="card">
		<div class="card-status bg-blue" style="position: inherit;"></div>
		<div class="card-header">Альбом пользователя: <?php echo $user['surname'] . " " . $user['name'] . " " . $user['midname'];?></div>
		<?php if($id == $_SESSION['id']){ ?>
		<div class="card-header">
			<div class="row justify-content-center" style="margin: auto;">
				<form name="albumuploadimg" method="post" enctype="multipart/form-data">
					<input type="file" id="img-upload" accept="image/*"/>
					<label for="img-upload" class="custom-file-upload" style="margin-top: 8px; margin-bottom: 8px;">
						<i class="fa fa-cloud-upload"></i> Добавить файл
					</label>
				</form>
			</div>
		</div>
		<?php } ?>
		<div class="card-body">
			<div class="row m-0">
			<?php if(count($imgs) > 0) {
				foreach($imgs as $img){ ?>
				<div class="col-md-3 col-sm-6 col-xs-12 p-2" id="imgs-column">
					<a id="<?php echo $img['id']?>" href="<?php echo $img['id']?>" data-toggle="modal" class="limit">
					<!--<a class="limit" data-fancybox="gallery" href="<?php echo $img['path']?>">-->
						<img src="<?php echo $img['path']?>" class="img-fluid">
					</a>
				</div>
			<?php } }else{ ?>
				<div style="display: center;">Изображения отсутствуют</div>
			<?php } ?>
			</div>
		</div>
		<!--<div class="modal" id="open-img-modal" tabindex="-1" role="dialog" aria-labelledby="open-img-modal-modalLabel" aria-hidden="true">
			<div class="modal-dialog album" role="img">
				<div class="modal-content">
					<div class="modal-header">
					</div>
				</div>
			</div>
		</div>-->
	<?php
}
else
{

}
?>