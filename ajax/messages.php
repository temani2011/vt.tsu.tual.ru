<?php

session_start();
$id = $_SESSION['id_groups'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
$dal = new DAL();
if($_SESSION['id'])
{
  if((isset($_POST['dname']) && isset($_POST['command'])) || (isset($_POST['did']) && isset($_POST['command'])))
  {
    $dname = $_POST['dname'];
    $part_ids = json_decode($_POST['arr']);
    $count = 0;
    switch ($_POST['command']) 
    {
      case 'create':
        foreach($part_ids as $id)
          $count++;
        $title = '';
        if($count == 1)
        {
          $title = "{$_SESSION['surname']} {$_SESSION['name']} {$_SESSION['midname']}-{$dname}";
        }
        else
        {
          $title = $dname;
        }
        $did = $dal->set_new_dialog($title, $_SESSION['id']);
        if(isset($did[0]['did']))
        {
          foreach($part_ids as $uid)
          {
            $dal->set_new_participant($did[0]['did'], $uid);
          }
          $dal->set_new_participant($did[0]['did'], $_SESSION['id']);
          if(!isset($_POST['msg'])) $msg = "system: {$_SESSION['surname']} {$_SESSION['name']} {$_SESSION['midname']} cоздал диалог {$title}";
          else $msg = $_POST['msg'];
          if($dal->set_message_in_dialog($_SESSION['id'], $did[0]['did'], $msg))
          { echo "Диалог успешно создан!"; exit(); }
        }
        echo "Не удалось добавить новый диалог!"; 
        exit();
      
      case 'update': 
        if($dal->upd_dialog($_POST['did'], $dname))
        {
          foreach($part_ids as $uid)
            $dal->del_participant($_POST['did'], $uid);
          echo "Диалог успешно обновлен!"; exit();
        }
        else{ echo "Не удалось название диалога!"; exit(); }

      case 'delete':
        $messages = $dal->get_dialog_messgaes($_POST['did']);
        $participants = $dal->get_participants_for_dialog($_POST['did']);
        foreach ($messages as $message)
          $dal->del_message($message['mid']);
        foreach ($participants as $participant)
          $dal->del_participant($participant['did'], $participant['uid']);
        if($dal->del_dialog($_POST['did']))
        { 
            echo "Диалог успешно удален!"; exit();
        }
        else { echo "Не удалось удалить диалог!"; exit(); }

        case 'add-part':
          foreach($part_ids as $uid)
            $dal->set_new_participant($_POST['did'], $uid);
          echo "Новые участники успешно добавлены в диалог!";
          exit();
    }
    exit();
  }
  if(isset($_GET['did']))
  {
    $messages = $dal->get_dialog_messgaes($_GET['did']);
    ?>   
    <div class="card" id="dialog-box">
      <div class="card-status bg-blue"></div>
      <div class="card-header">
        <div class="recent_heading" style="width: 100%;">
          <h4><?php $fio = $_SESSION['surname'].' '.$_SESSION['name'].' '.$_SESSION['midname']; $title = $messages[0]['title']; $title = str_replace('-','', str_replace($fio,'',$title)); echo $title; ?></h4>
        </div>
        <?php if($messages[0]['creator_id'] == $_SESSION['id']) {  ?>
        <div class="add_dialog">
          <span class="input-group-addon">
            <button type="button" data-toggle="dropdown" id="btn-settings-dialog"><i class="fa fa-cog" aria-hidden="true"></i></button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <?php if($dal->get_participants_count($_GET['did'])[0]['cp'] > 2) { ?>
              <a class="dropdown-item" href="#" id = "dd-dialog-add-participants" data-toggle="modal" data-target="#add-participants-dialog-modal">Добавить участников</a> 
              <a class="dropdown-item" href="#" id = "dd-dialog-update" data-toggle="modal" data-target="#settings-dialog-modal">Настройка диалога</a>
              <?php } ?>
              <a class="dropdown-item" href="#" id = "dd-dialog-delete">Удалить диалог</a>
            </div>
          </span>
        </div>
      <?php } ?>
      </div>
      <div class="card-body msg_history" id="msg_history">
        <div class="row msg-row" id="messages">
          <?php foreach($messages as $message){ if(strpos($message['text'], 'system:')) continue; ?>
          <table id="chats" class="table table-striped">
            <tbody>
              <tr>
                <td valign="top">
                  <div style="display: inline;">
                    <strong><?php echo ($message['surname'] . " " . $message['name'] . " " . $message['midname']);?></strong>
                    <div><?php echo $message['text']; ?></div>
                    <td align="right" valign="top">
                  </div>
                  <?php $date = new DateTime($message['date']); $date=$date->format('H:i d.m.Y'); echo $date; ?>
                </td>
              </tr>
            </tbody>
          </table>
          <?php }?>
        </div>
      </div>
      <div class="card-footer p-3">
        <div class="form-group pb-0">
          <textarea class="form-control" id = "msg" placeholder="Введите сообщение" style="margin-top: 0px;margin-bottom: 0px;height: 70px;"></textarea>
        </div>
      </div>
      
      <input type="hidden" id = "userId" value="<?php echo $_SESSION['id']; ?>">
      <input type="hidden" id = "dId" value="<?php echo $_GET['did']; ?>">
      
      <div class="modal" id="settings-dialog-modal" tabindex="-1" role="dialog" aria-labelledby="settings-dialog-modalLabel" aria-hidden="true">
        <div class="modal-dialog custom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settings-dialog-modalLabel">Настройка диалога</h5>
                    <button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
                        &times;
                    </button>
                </div>
                <div class="modal-header">
                  <div class="stylish-input-group-modal">
                    <span class = "search-icon-modal"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="search-bar modal-user-search" name="users-search-bar" placeholder="Поиск" style="padding-left: 25px;">
                  </div>
                </div>
                <div class="modal-header" id="dialog-name">
                  <div class="stylish-input-group-modal">
                    <p style="display: inline;">Название диалога: </p>
                    <input type="text" class="dialog-name" id="dialog-name-text-input-update" value="<?php echo $messages[0]['title'];?>">
                  </div>
                </div>
                <div class="modal-body p-0" style="overflow-y: scroll; height: 400px;">
                  <?php $participants = $dal->get_participants_for_dialog($_GET['did']); foreach($participants as $participant) {
                    if($participant['id1']!=$_SESSION['id'])
                      { ?>
                      <div class="chat_list_modal">
                        <div class="chat_people">
                          <div class="chat_img">   
                            <?php if(!$participant['img']) { ?>
                              <img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
                            <?php } else { ?>
                              <img alt="User Pic" src="<?php echo $participant['img'];?>"  width="300" height="auto" class="rounded-circle"> 
                            <?php } ?>  
                          </div>
                            <div class="chat_ib">
                              <h5><?php echo ($participant['surname'] . " " . $participant['name'] . " " . $participant['midname']);?></h5>
                              <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="modal-user-checkbox" name="example-checkbox2" uname ="<?php echo $participant['surname']?>" value="<?php echo $participant['id1']?>" checked>
                              </label>
                            </div>
                          </div>
                        </div>
                    <?php }
                  } ?>
                  </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-update-dialog">Применить</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="add-participants-dialog-modal" tabindex="-1" role="dialog" aria-labelledby="add-participants-dialog-modalLabel" aria-hidden="true">
      <div class="modal-dialog custom" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="add-participants-dialog-modalLabel">Добавление участников</h5>
            <button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
              &times;
            </button>
          </div>
          <div class="modal-header">
            <div class="stylish-input-group-modal">
              <span class = "search-icon-modal"><i class="fa fa-search" aria-hidden="true"></i></span>
              <input type="text" class="search-bar modal-user-search" name="users-search-bar" placeholder="Поиск" style="padding-left: 25px;">
            </div>
          </div>
          <div class="modal-body p-0" style="overflow-y: scroll; height: 400px;">
            <?php $participants = $dal->get_not_participants_for_dialog($_GET['did']); foreach($participants as $participant) {
            if($participant['role'] != 'admin') {   ?>
              <div class="chat_list_modal">
                <div class="chat_people">
                  <div class="chat_img">
                    <?php if(!$participant['img']) { ?>
                      <img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
                    <?php } else { ?>
                      <img alt="User Pic" src="<?php echo $participant['img'];?>"  width="300" height="auto" class="rounded-circle"> 
                    <?php } ?> 
                  </div>
                  <div class="chat_ib">
                    <h5><?php echo ($participant['surname'] . " " . $participant['name'] . " " . $participant['midname']);?></h5>
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="modal-user-checkbox" id="modal-user-checkbox-add-participant" name="example-checkbox2" uname ="<?php echo $participant['surname']?>" value="<?php echo $participant['id']?>">
                    </label>
                  </div>
                </div>
              </div>
              <?php } } ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="btn-add-participants-dialog">Добавить</button>
            </div>
          </div>
        </div>
      </div>
    <?php
    exit();
  }
  $dialogs = $dal->get_user_dialogs($_SESSION['id']);?>
  <div class="card">
    <div class="card-status bg-blue"></div>
  	<div class ="card-header">
  		<div class="recent_heading">
  			<h4>Сообщения</h4>
  		</div>
  		<div class="srch_bar">
  			<div class="stylish-input-group">
          <span class = "search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
  				<input type="text" class="search-bar" id="messages-search-bar" placeholder="Поиск" style="
    padding-left: 25px;">
  			</div>
  		</div>
      <div class="add_dialog">
        <span class="input-group-addon">
          <button type="button" data-toggle="modal" data-target="#create-message-dialog-modal" id="btn-add-dialog"><i class="fa fa-plus" aria-hidden="true"></i>
        </span>
      </div>
  	</div>
  	<div class="inbox_chat">
      <?php foreach($dialogs as $dialog){ ?>
    		<div class="chat_list" did="<?php echo $dialog['did'];?>">
    			<div class="chat_people">
    				<div class="chat_img">
             <?php $participants = $dal->get_participants_for_dialog($dialog['did']);
             if(count($participants) == 2) { 
             foreach ($participants as $participant) {
              if($participant['id1']!=$_SESSION['id']){
                if(!$participant['img']) { ?>
                <img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
                <?php } else { ?>
                <img alt="User Pic" src="<?php echo $participant['img'];?>"  width="300" height="auto" class="rounded-circle"> 
              <?php }
              } } } else { ?> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> <?php } ?>
            </div>
    				<div class="chat_ib">
    					<h5><div><?php $fio = $_SESSION['surname'].' '.$_SESSION['name'].' '.$_SESSION['midname']; $title = $dialog['title']; $title = str_replace('-','', str_replace($fio,'',$title)); echo $title; ?><div class="chat_participants">Участников: <?php echo $dal->get_participants_count($dialog['did'])[0]['cp'];?></div><div><span class="chat_date"><?php $date = new DateTime($dialog['date']); $date=$date->format('H:i d.m.Y'); echo $date;?></span></h5>
    					<p><?php echo $dialog['text'];?></p>
    				</div>
            <!--
            <div class = "settings_btn">
              <i class="fa fa-cog" aria-hidden="true"></i>
            </div>
            -->
    			</div>
    		</div>
      <?php } ?>
  	</div>
  </div>
  <div class="modal" id="create-message-dialog-modal" tabindex="-1" role="dialog" aria-labelledby="create-message-dialog-modalLabel" aria-hidden="true">
        <div class="modal-dialog custom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-message-dialog-modalLabel">Создание диалога</h5>
                    <button type="button" class="close" data-dismiss="modal" id="btn-close-modal">
                        &times;
                    </button>
                </div>
                <div class="modal-header">
                  <div class="stylish-input-group-modal">
                    <span class = "search-icon-modal"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="search-bar modal-user-search" name="users-search-bar" placeholder="Поиск" style="padding-left: 25px;">
                  </div>
                </div>
                <div class="modal-header" id="hidden-dialog-name" style="display: none;">
                  <div class="stylish-input-group-modal">
                    <p style="display: inline;">Название диалога: </p>
                    <input type="text" class="dialog-name" id="dialog-name-text-input-create">
                  </div>
                </div>
                <div class="modal-body p-0" style="overflow-y: scroll; height: 400px;">
                  <?php $users = $dal->get_users_role('all'); foreach($users as $user){ 
                    if($user['id']!=$_SESSION['id'] && $user['role'] != 'admin')
                      { ?>
                      <div class="chat_list_modal">
                        <div class="chat_people">
                          <div class="chat_img">
                          <?php if(!$user['img']) { ?>
                            <img alt="User Pic" src="https://ptetutorials.com/images/user-profile.png"  width="250" height="auto" class="rounded-circle">
                          <?php } else { ?>
                            <img alt="User Pic" src="<?php echo $user['img'];?>"  width="300" height="auto" class="rounded-circle"> 
                          <?php } ?> 
                          </div>
                            <div class="chat_ib">
                              <h5><?php echo ($user['surname'] . " " . $user['name'] . " " . $user['midname']);?></h5>
                              <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="modal-user-checkbox" name="example-checkbox1" uname ="<?php echo $user['surname']?>" value="<?php echo $user['id1']?>">
                              </label>
                            </div>
                          </div>
                        </div>
                    <?php }
                  } ?>
                  </div>
                <div class="modal-footer">
                    <textarea class="form-control" id = "msg-modal" placeholder="Введите сообщение" style="margin-top: 0px;margin-bottom: 0px;height: 70px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn-create-dialog">Создать</button>
                </div>
            </div>
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