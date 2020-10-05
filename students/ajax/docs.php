<?php
setlocale(LC_ALL,'en_US.UTF-8');
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$res = array();
$dal = new DAL();
if($_SESSION['id'])
{
	if($_POST['doc_id'] && $_POST['command'])
	{
		$doc_path =  $dal->get_doc($_POST['doc_id']);
		$dir = $_SERVER['DOCUMENT_ROOT']  . iconv('utf-8' , 'windows-1251', $doc_path[0]['cat_path']) . '/';
		$file = $dir . $doc_path[0]['name'];
		$ext = pathinfo($doc_path[0]['name'])['extension'];
		switch ($_POST['command']) {
			case 'update':
				if (!rename($file, $dir . $_POST['new_name'] . '.' .  $ext))
				{
					echo ("Error rename $file"); exit();
				}
				else
				{	
					if($dal->upd_file($_POST['doc_id'], $_POST['new_name'] . '.' .  $ext))
						echo "Файл успешно переименован!"; exit();
				}
			case 'delete': 
				if (!unlink($file))
				{
					echo ("Error deleting $file"); exit();
				}
				else
				{	
					if($dal->del_file($_POST['doc_id'])) 
						echo "Файл успешно удален!"; exit();
				}
		}
		exit();
	}
	if (isset($_FILES['file']['tmp_name']))
	{
		$catalog_name = $dal->get_catalog($_POST['catalog_id'])[0]['cat_path'];
		$catalog_name = iconv('utf-8' , 'windows-1251', $catalog_name);
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $catalog_name . '/' . $_FILES['file']['name'])) 
		{   
			move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $catalog_name . '/' . $_FILES['file']['name']);
			if($dal->set_new_file($_POST['catalog_id'], $_SESSION['id'], $_FILES['file']['name'], $_SESSION['role'])) echo 'Файл успешно загружен!';
			exit();
		}
		else
		{
			echo "Файл с таким именем уже существует!"; exit();
		}
		exit();
	}
	$catalogs = $dal->get_catalogs($_SESSION['role']); ?>
	<div class="card-status bg-blue" style="position: inherit;"></div>
	<table class="table table-condensed" id="catalogs">
		<thead class="thead">
			<tr>
				<th scope="col">#</th>
				<th scope="col">Каталог</th>
				<th scope="col">Файлы</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$counter = 0;
			foreach ($catalogs as $catalog) 
			{
				$counter++; ?>   
				<th scope="row" style="text-align: center;"><?php echo $counter;?></th>
				<td><a class="doc_a" href="#docs.php?catalog=<?php echo $catalog['id'];?>"><?php echo $catalog['title'];?></a></td>
				<td style="text-align: center;"><?php echo $catalog['count'];?></td>
			</tr>
			<?php	} ?> 
		</tbody>
	</table>

<?php if(isset($_GET['catalog']))
{
	$catalog_id = $_GET['catalog']; 
	$docs = $dal->get_docs($catalog_id);
	if(!empty($docs)) { ?>
		<div class="card-status bg-blue" style="position: inherit;"></div>
		<table class="table table-condensed" id="docs">
			<thead class="thead">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Файл</th>
					<th scope="col">Тип</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$counter = 0;
				foreach ($docs as $doc) 
				{
					$counter++; ?>
					<th scope="row" style="text-align: center;"><?php echo $counter;?></th>
					<td><a class="doc_bs" href="<?php echo ($doc['cat_path'].'/'.$doc['name'])?>" download><?php $path = pathinfo($doc['name']); echo  basename($doc['name'],'.'.$path['extension']);?></a></td>
					<td style="text-align: center;"><?php echo $path['extension'];?></td>
					<td style="text-align: center;">
						<?php if($doc['id_user'] == $_SESSION['id']){ ?>
							<div id="btn-update-docs" class="btn-update-delete" docid="<?php echo $doc['id'];?>"><i class="fa fa-pencil" aria-hidden="true"></i></div>
							<div id="btn-delete-docs" class="btn-update-delete" docid="<?php echo $doc['id'];?>"><i class="fa fa-times" aria-hidden="true"></i></div>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
		<div class="modal" id="update-doc-dialog-modal" tabindex="-1" role="dialog" aria-labelledby="update-doc-dialog-modalLabel" aria-hidden="true">
        <div class="modal-dialog-docs" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="update-doc-dialog-modalLabel">Сменить имя файла</h5>
                    <button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
                        &times;
                    </button>
                </div>
                <div class="modal-header">
                  <div class="stylish-input-group-modal">
                    <p style="display: inline;">Новое имя: </p>
                    <input type="text" class="dialog-name" id="doc-name-text-input">
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-update-accept-doc">Изменить</button>
                </div>
            </div>
        </div>
    </div>
	<?php 
}
else
{
	?>
	<div class="card-status bg-blue" style="position: inherit;"></div>
	<table class="table table-condensed" id="docs">
		<thead class="thead">
			<tr>
				<th scope="col">#</th>
				<th scope="col">Файл</th>
				<th scope="col">Тип</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
				<td style="text-align: center;">В каталоге отсутствуют файлы</td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<?php
}
if($dal->get_catalog($catalog_id)[0]['cat_rwgrant'] == 'rw')
{ 
	?>
	<div class="row justify-content-center">
		<form name="uploadimages" method="post" enctype="multipart/form-data">
			<input type="file" id="file-upload" />
			<label for="file-upload" class="custom-file-upload">
    		<i class="fa fa-cloud-upload"></i> Добавить файл
			</label>
			<!--<button type="button" class="btn btn-success" id="btn-add-docs"><i class="fa fa-plus" aria-hidden="true" style="float: center;"></i> Добавить файл</button>-->
		</form>
	</div>
	<?php
}
}
}
else
	{	$err ='
<div class="alert alert-warning" role="alert">
У вас нет прав доступа для просмотра страницы, пожалуйста авторизируйтесь!
</div>';
echo $err;
}
?>