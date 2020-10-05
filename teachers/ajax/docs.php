<?php
setlocale(LC_ALL,'en_US.UTF-8');
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$res = array();
$dal = new DAL();
if($_SESSION['id'])
{
	if((isset($_POST['cat_id']) && isset($_POST['command'])) || (isset($_POST['cat_name']) && isset($_POST['command'])))
	{
		$base_folder = $_SERVER['DOCUMENT_ROOT'] . '/docs/';
		$cat_name = $_POST['cat_name'];
		$cat_id = $_POST['cat_id'];
		$command = $_POST['command'];
		switch ($command) {
			case 'modal':
			$catalog = $dal->get_catalog($cat_id);
			if($catalog) echo json_encode($catalog[0]);
			break;

			case 'create':
				if (!file_exists($base_folder . $cat_name)) {
					if(!mkdir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . iconv('utf-8' , 'windows-1251', $cat_name), 0777, true)) { echo "Не удалось создать каталог!"; break; }
					if($dal->set_new_catalog($cat_name, '/docs/' . $cat_name , $_POST['cat_rw'], $_POST['cat_role'], $_SESSION['id']))
						echo "Каталог успешно создан!";
				}
				else echo "Такой каталог уже существует!";
				break;
			
			case 'update':
				if (!rename($base_folder . iconv('utf-8' , 'windows-1251', $cat_name) , $base_folder . iconv('utf-8' , 'windows-1251', $_POST['new_name'])))
				{
					echo ("Error rename $file"); exit();
				}
				else
				{	
					if($dal->upd_catalog($cat_id, $_POST['new_name'], $_POST['cat_rw'], $_POST['cat_role'], '/docs/'. $_POST['new_name']))
						echo "Файл успешно обновлен!" . $_POST['new_name'] . $_POST['cat_rw'] . $_POST['cat_role'] . $base_folder . $_POST['new_name'];
				}
				break;

			case 'delete':
				$dir = $base_folder . iconv('utf-8' , 'windows-1251', $cat_name);
				$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new RecursiveIteratorIterator($it,
					RecursiveIteratorIterator::CHILD_FIRST);
				foreach($files as $file) {
					if ($file->isDir()){
						rmdir($file->getRealPath());
					} else {
						unlink($file->getRealPath());
					}
				}
				if(rmdir($dir))
				{
					if($dal->del_docs_from_ctalog($cat_id))
					{
						if($dal->del_ctalog($cat_id))
							echo "Каталог успешно удален!";
					}	
					else  echo "Не удалось удалить каталог!";
				}
				break;
		}
		exit();
	}
	if(isset($_POST['doc_id']) && isset($_POST['command']))
	{
		$doc_path =  $dal->get_doc($_POST['doc_id']);
		$dir = $_SERVER['DOCUMENT_ROOT']  . iconv('utf-8' , 'windows-1251', $doc_path[0]['cat_path']) . '/';
		$file = $dir . iconv('utf-8' , 'windows-1251', $doc_path[0]['name']);
		$ext = pathinfo($doc_path[0]['name'])['extension'];
		switch ($_POST['command']) {
			case 'update':
				if (!rename($file, $dir . iconv('utf-8' , 'windows-1251', $_POST['new_name']) . '.' .  $ext))
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
			move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $catalog_name . '/' . iconv('utf-8' , 'windows-1251', $_FILES['file']['name']));
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
				<th></th>
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
				<td style="text-align: center;">
					<?php if($catalog['user_id'] == $_SESSION['id']){ ?>
						<div id="btn-update-cat" class="btn-update-delete" catid="<?php echo $catalog['id'];?>"><i class="fa fa-pencil" aria-hidden="true"></i></div>
						<div id="btn-delete-cat" class="btn-update-delete" catid="<?php echo $catalog['id'];?>"><i class="fa fa-times" aria-hidden="true"></i></div>
					<?php } ?>
				</td>
			</tr>
			<?php } ?> 
		</tbody>
	</table>
	<div class="row justify-content-center">
		<button type="button" class="custom-file-upload" data-toggle="modal" data-target="#create-catalog-modal"><i class="fa fa-plus" aria-hidden="true"></i> Добавить каталог </button>
	</div>
	<div class="modal" id="create-catalog-modal" tabindex="-1" role="dialog" aria-labelledby="create-catalog-modal-modalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="create-catalog-modalLabel">Создание каталога</h5>
					<button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
						&times;
					</button>
				</div>
				<div class="modal-header" id="catalog-name">
					<div class="stylish-input-group-modal">
						<p style="display: inline;">Название каталога: </p>
						<input type="text" class="dialog-name-text" id="catalog-name-text-input-create" style="width:68%">
						<div style="padding-top: 15px">Видимость для студентов <input type="checkbox" name="rolecheck"></div>
						<div style="padding-top: 15px">Возможность добавлять файлы <input type="checkbox" name="rwcheck" disabled="true"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" nid="" id="btn-create-catalog">Применить</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="update-cat-dialog-modal" tabindex="-1" role="dialog" aria-labelledby="update-cat-dialog-modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="update-cat-dialog-modalLabel">Сменить имя каталога</h5>
                    <button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
                        &times;
                    </button>
                </div>
                <div class="modal-header">
                  <div class="stylish-input-group-modal">
                    <p style="display: inline;">Новое имя: </p>
                    <input type="text" class="dialog-name-text" id="cat-name-text-input">
                    <div style="padding-top: 15px">Видимость для студентов <input type="checkbox" name="rolecheckU"></div>
					<div style="padding-top: 15px">Возможность добавлять файлы <input type="checkbox" name="rwcheckU"></div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-update-accept-cat">Изменить</button>
                </div>
            </div>
        </div>
    </div>

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
						<?php if($doc['user_id'] == $_SESSION['id'] || $doc['id_user'] == $_SESSION['id']){ ?>
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
                    <input type="text" class="dialog-name-text" id="doc-name-text-input">
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
} ?>
    <div class="row justify-content-center">
		<form name="uploadimages" method="post" enctype="multipart/form-data">
			<input type="file" id="file-upload" />
			<label for="file-upload" class="custom-file-upload">
    		<i class="fa fa-cloud-upload"></i> Добавить файл
			</label>
		</form>
	</div>
<?php 
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