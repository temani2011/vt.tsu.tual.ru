<?php
// Include DAL
// Database
define ( 'DB_HOST', 'localhost' );
define ( 'DB_USER', 'mysql' );
define ( 'DB_PASSWORD', 'mysql' );
define ( 'DB_NAME', 'vt_tsu_tula' );

mb_internal_encoding("UTF-8");
class DALQueryResultt {

  private $_results = array();

  public function __construct(){}

  public function __set($var,$val){
    $this->_results[$var] = $val;
  }

  public function __get($var){  
    if (isset($this->_results[$var])){
      return $this->_results[$var];
    }
    else{
      return null;
    }
  }
}

class DAL {

  public function __construct(){}
  /*login*/
  public function set_token($token, $info, $uid)
  {   
    $sql = "INSERT INTO tokens(token, info, user_id) VALUES ('$token', '$info', '$uid')";
    return $this->query($sql);
  }
  public function get_token($token)
  {
    $sql = "SELECT * FROM tokens WHERE token = '$token'";
    return $this->query($sql);
  }
  public function get_user_by_token($token)
  {
    $sql = "SELECT * FROM tokens WHERE token = '$token'";
    return $this->query($sql);
  }
  public function del_token($token)
  {
    $sql = "DELETE FROM tokens WHERE token = '$token'";
    return $this->query($sql);
  }
  /*blog*/
  public function set_new_blog($id)
  {   
    $sql = "INSERT INTO blogs(id, user_id, description) VALUES (NULL,$id,NULL)";
    return $this->query($sql);
  }
  public function get_blog_posts($id)
  {
    $sql = "SELECT * FROM blogs JOIN posts ON blogs.id = posts.blog_id WHERE blogs.user_id = '$id'";
    return $this->query($sql);
  }
  public function get_blog_post($id)
  {
    $sql = "SELECT * FROM posts WHERE id = '$id'";
    return $this->query($sql);
  }
  public function set_blog_post($name, $txt, $bid)
  {
    $sql = "INSERT INTO posts(id, name, text, date, blog_id, com_id) VALUES (NULL, '$name', '$txt', CURRENT_TIMESTAMP, '$bid', '0')";
    return $this->query($sql);
  }
  public function upd_blog_post($id, $name, $txt)
  {
    $sql = "UPDATE posts SET text = '$txt', name = '$name' WHERE id = '$id'";
    return $this->query($sql);
  }
  public function del_blog_post($id)
  {
    $sql = "DELETE FROM posts WHERE id = '$id'";
    return $this->query($sql);
  }
  /*album.php*/
  public function set_new_album_img($uid, $path)
  {
    $sql = "INSERT INTO imgs(id, description, date, path, id_user) VALUES (NULL,'',CURRENT_TIMESTAMP,'$path','$uid')";
    return $this->query($sql);
  }
  public function set_comment($iid, $uid, $text)
  {
    $sql = "INSERT INTO comments(id, id_user, id_where, text, date_c) VALUES (NULL,'$uid', '$iid', '$text' , CURRENT_TIMESTAMP)";
    return $this->query($sql);
  }
  public function get_comments($iid)
  {
    $sql = "SELECT * FROM comments JOIN users ON comments.id_user = users.id WHERE id_where = '$iid'";
    return $this->query($sql);
  }
  public function set_like($iid, $uid)
  {
    $sql = "INSERT INTO likes(id, id_img, id_user, date) VALUES (NULL,'$iid', '$uid', CURRENT_TIMESTAMP)";
    return $this->query($sql);
  }
  public function del_like($iid, $uid)
  {
    $sql = "DELETE FROM likes WHERE id_img = '$iid' AND id_user = '$uid'";
    return $this->query($sql);
  }
  public function get_likes($iid)
  {
    $sql = "SELECT * FROM likes WHERE id_img = '$iid'";
    return $this->query($sql);
  }
    public function get_like($iid, $uid)
  {
    $sql = "SELECT * FROM likes WHERE id_img = '$iid' AND id_user = '$uid'";
    return $this->query($sql);
  }
  public function get_imgs($uid)
  {
    $sql = "SELECT * FROM imgs WHERE id_user = '$uid'";
    return $this->query($sql);
  }

 /*progress.php*/
  public function get_sum_attendance($sid, $uid)
  {
    $sql = "SELECT SUM(attend) as attend_sum, SUM(score) as score_sum, subjects.hours, COUNT(score) as score_count FROM attendance JOIN subjects ON attendance.sid = subjects.id WHERE uid = '$uid' AND sid = '$sid'";
    return $this->query($sql);
  }

  public function del_attendance($sid, $uid)
  {
    $date = date('Y-m-d');
    $sql = "DELETE FROM attendance WHERE uid = '$uid' AND sid = '$sid' AND date = '$date'";
    return $this->query($sql);
  }

  public function upd_attendance($sid, $uid, $score, $attend)
  {
    $date = date('Y-m-d');
    $sql = "UPDATE attendance SET score = '$score', attend = '$attend' WHERE uid = '$uid' AND sid = '$sid' AND date = '$date'";
    return $this->query($sql);
  }

  public function set_attendance($sid, $uid, $score, $attend)
  {
    $date = date('Y-m-d');
    $sql = "INSERT INTO attendance(uid, sid, date, score, attend) VALUES ('$uid', '$sid', '$date', '$score', '$attend')";
    return $this->query($sql);
  }

  public function get_attendance($sid, $uid)
  {
    $date = date('Y-m-d');
    $sql = "SELECT * FROM users JOIN attendance ON users.id = attendance.uid JOIN subjects ON subjects.id = attendance.sid WHERE uid = '$uid' AND sid = '$sid' AND date = '$date'";
    return $this->query($sql);
  }

  /*profile.php*/
  public function upd_new_user_img($id, $new_img)
  {
    $sql = "UPDATE users SET img = '$new_img' WHERE id = '$id'";
    return $this->query($sql);
  }

  public function upd_group($id, $group_number, $specialty, $id_subfaculty)
  {
    $sql = "UPDATE groups SET group_number = '$group_number', specialty = '$specialty', id_subfaculty = '$id_subfaculty' WHERE id = '$id'";
    return $this->query($sql);
  }

  public function del_group($group_number)
  {
    $sql = "DELETE FROM groups WHERE group_number = '$group_number'";
    return $this->query($sql);
  }

  public function get_group($id)
  {
    $sql = "SELECT * FROM groups WHERE id = '$id'";
    return $this->query($sql);
  }

  public function upd_user($id, $surname, $name, $midname, $birth_date, $phone, $email, $password, $city, $role, $id_groups)
  {
    if($role == 'teacher') $sql = "UPDATE users SET surname = '$surname', name = '$name', midname = '$midname', birth_date = '$birth_date', phone = '$phone', email = '$email', password = '$password', city = '$city', role = '$role', id_groups = NULL WHERE id = '$id'";
    else
      $sql = "UPDATE users SET surname = '$surname', name = '$name', midname = '$midname', birth_date = '$birth_date', phone = '$phone', email = '$email', password = '$password', city = '$city', role = '$role', id_groups = '$id_groups' WHERE id = '$id'";
    return $this->query($sql);
  }

  public function del_user($user_id)
  {
    $sql = "DELETE FROM users WHERE id = '$user_id'";
    return $this->query($sql);
  } 

  public function set_new_group($group_number, $specialty, $id_subfaculty)
  {
    $sql = "INSERT INTO groups(id, group_number, specialty, description, id_subfaculty) VALUES (NULL, '$group_number', '$specialty', '', '$id_subfaculty')";
    return $this->query($sql);
  }

  public function set_new_user($id, $surname, $name, $midname, $birth_date, $phone, $email, $password, $city, $role, $id_subfaculty, $id_groups)
  {
    if($role == 'teacher' || $role == 'staff') $sql = "INSERT INTO users(id, surname, name, midname, birth_date, phone, email, password, city, date_of_registration, role, id_subfaculty, id_groups) VALUES ($id, '$surname', '$name', '$midname', '$birth_date', '$phone', '$email', '$password', '$city', CURRENT_TIMESTAMP, '$role', '$id_subfaculty', NULL)";
    else
      $sql = "INSERT INTO users(id, surname, name, midname, birth_date, phone, email, password, city, date_of_registration, role, id_subfaculty, id_groups) VALUES ($id, '$surname', '$name', '$midname', '$birth_date', '$phone', '$email', '$password', '$city', CURRENT_TIMESTAMP, '$role', '$id_subfaculty', '$id_groups')";
    return $this->query($sql);
  }

  public function get_subfaculty()
  {
    $sql = "SELECT * FROM subfaculty";
    return $this->query($sql);
  }
  //////////////////////////////////////

  public function get_user($id)
  {
    $sql = "SELECT * FROM users WHERE id = '$id'";
    return $this->query($sql);
  }

  /*journal.php*/
  public function get_all_groups()
  {
    $sql = "SELECT groups.id as id1, groups.*, subfaculty.* FROM groups JOIN subfaculty ON groups.id_subfaculty = subfaculty.id";
    return $this->query($sql);
  }

  /*docs.php*/
  /////
  public function get_last_docs()
  {
    $sql = "SELECT * FROM docs JOIN catalogs ON docs.id_catalog = catalogs.id ORDER BY docs.id DESC LIMIT 5";
    return $this->query($sql);
  }
  /////
  public function upd_catalog($cat_id, $new_name, $cat_rw, $cat_role, $cat_path)
  {
    $sql = "UPDATE catalogs SET title = '$new_name', cat_role = '$cat_role', cat_rwgrant = '$cat_rw', cat_path = '$cat_path' WHERE id = '$cat_id'";
    return $this->query($sql);
  }

  public function del_ctalog($cat_id)
  {
    $sql = "DELETE FROM catalogs WHERE id = '$cat_id'";
    return $this->query($sql);
  }

  public function del_docs_from_ctalog($cat_id)
  {
    $sql = "DELETE FROM docs WHERE id_catalog = '$cat_id'";
    return $this->query($sql);
  }

  public function set_new_catalog($cat_name, $cat_path, $cat_rw, $cat_role, $user_id)
  {
    $sql = "INSERT INTO catalogs(id, title, cat_role, cat_rwgrant, cat_path, user_id) VALUES (NULL, '$cat_name', '$cat_role', '$cat_rw', '$cat_path', '$user_id')";
    return $this->query($sql);
  }

  public function get_catalog($id)
  {
    $sql = "SELECT * FROM catalogs WHERE id = '$id'";
    return $this->query($sql);
  }

  //docsList.php
  public function get_user_docs($id)
  {
    $sql = "SELECT * FROM catalogs JOIN docs ON catalogs.id = docs.id_catalog WHERE docs.id_user = '$id'";
    return $this->query($sql);
  }
  //

  public function get_doc($id)
  {
    $sql = "SELECT * FROM docs JOIN catalogs ON docs.id_catalog = catalogs.id WHERE docs.id = '$id'";
    return $this->query($sql);
  }
  
  public function get_catalogs($role)
  {
    if($role == 'student') $sql = "SELECT catalogs.* , (SELECT COUNT(*) FROM docs WHERE id_catalog = catalogs.id) as count FROM catalogs WHERE cat_role = 'student'";
    else $sql = "SELECT catalogs.* , (SELECT COUNT(*) FROM docs WHERE id_catalog = catalogs.id) as count FROM catalogs";
    return $this->query($sql);
  }

  public function get_docs($id_catalog)
  {
    $sql="SELECT * FROM catalogs JOIN docs ON docs.id_catalog = catalogs.id WHERE catalogs.id = '$id_catalog'";
    return $this->query($sql);
  }

  public function upd_file($id, $new_name)
  {
    $sql = "UPDATE docs SET name = '$new_name' WHERE id = '$id'";
    return $this->query($sql);
  }

  public function del_file($id)
  {
    $sql = "DELETE FROM docs WHERE id = '$id';";
    return $this->query($sql);
  }

  public function set_new_file($id_catalog, $id_user, $name, $role)
  {
    $sql = "INSERT INTO docs (id, name, role, id_user, id_catalog) VALUES (NULL, '$name', '$role', '$id_user', '$id_catalog');";
    return $this->query($sql);
  }

  /*messages.php*/

  public function get_not_participants_for_dialog($did)
  {
    $sql = "SELECT users.* FROM users WHERE users.id NOT IN (SELECT participants.uid FROM participants WHERE participants.did = '$did')";
    return $this->query($sql);
  }

  public function del_dialog($id)
  {
    $sql = "DELETE FROM dialogs WHERE id ='$id'";
    return $this->query($sql);
  }

  public function del_message($id)
  {
    $sql = "DELETE FROM messages WHERE id ='$id'";
    return $this->query($sql);
  }

  public function del_participant($did, $uid)
  {
    $sql = "DELETE FROM participants WHERE did = '$did' AND uid ='$uid'";
    return $this->query($sql);
  }

  public function upd_dialog($did, $dname)
  {
    $sql = "UPDATE dialogs SET title = '$dname' WHERE id = '$did'";
    return $this->query($sql);
  }

  public function set_new_participant($did, $uid)
  {
    $sql = "INSERT INTO participants (did, role, uid) VALUES ($did, '', '$uid');";
    //echo $sql;
    return $this->query($sql);
  }

  public function set_new_dialog($title, $creator_id)
  {
    $sql = "INSERT INTO dialogs (id, title, cover, creator_id) VALUES (NULL, '$title', '', '$creator_id');";
    //echo $sql;
    $err = $this->query($sql);
    $sql = "SELECT id as did FROM dialogs ORDER BY id DESC LIMIT 1";
    //echo $sql;
    return $this->query($sql);
  }

  public function set_message_in_dialog($id, $did, $msg)
  {
    $sql = "INSERT INTO messages (id, text, date, is_read, role, sender_id, dialog_id) VALUES (NULL, '$msg', CURRENT_TIMESTAMP, '0', '', '$id', '$did')";
    //echo $sql;
    return $this->query($sql);
  }

  public function get_user_information_by_id($id)
  {
    $sql = "SELECT * FROM users WHERE id = '$id'";
    return $this->query($sql);
  }

  public function get_dialog_messgaes($did)
  {
    $sql = "SELECT messages.id as mid, messages.*, dialogs.*, users.*  FROM dialogs JOIN messages ON dialogs.id = messages.dialog_id JOIN users ON users.id = messages.sender_id WHERE dialogs.id = '$did' ORDER BY messages.id ASC";
    return $this->query($sql);
  }

  public function get_participants_count($did)
  {
    $sql = "SELECT count(*) as cp FROM participants WHERE did = '$did'";
    return $this->query($sql);
  }

  public function get_user_dialogs($id)
  {
    $sql = "SELECT * FROM dialogs JOIN participants ON dialogs.id = participants.did JOIN users ON users.id = participants.uid JOIN (SELECT  dialog_id, date, text FROM messages s1 WHERE date = (SELECT MAX(date) FROM messages s2 WHERE s1.dialog_id = s2.dialog_id) GROUP BY dialog_id) as t1 ON t1.dialog_id = dialogs.id WHERE users.id = '$id' ORDER BY t1.date DESC";
    return $this->query($sql);
  }

  public function get_participants_for_dialog($did)
  {
    $sql = "SELECT users.id as id1, users.*, participants.*, dialogs.* FROM users JOIN participants ON users.id = participants.uid JOIN dialogs ON dialogs.id = participants.did WHERE dialogs.id = '$did'";
    return $this->query($sql);
  }
  /*caf.php*/
  public function get_users_role_and_group($role, $group)
  {
    if($group == "all") $sql = "SELECT users.id as id1, users.*,  groups.* FROM users JOIN groups ON users.id_groups = groups.id WHERE users.role = '$role'";
    else $sql = "SELECT users.id as id1, users.*,  groups.* FROM users JOIN groups ON users.id_groups = groups.id WHERE groups.group_number = '$group' AND users.role = '$role'";
    return $this->query($sql);
  }

  public function get_users_role($role)
  {
    if($role == "all" or !$role) $sql = "SELECT users.id as id1, users.* FROM users";
    else $sql = "SELECT users.id as id1 , users.* FROM users WHERE role = '$role'";
    return $this->query($sql);
  }

  public function get_users_groups($id)
  {
    if($id==-1)
    {
      $sql = "SELECT * FROM groups;";
    }
    else
    {
      $sql = "SELECT * FROM users JOIN groups ON users.id_groups = groups.id WHERE users.id = '$id'";
    }
    return $this->query($sql);
  }
  
  public function get_user_information($email, $password){
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    return $this->query($sql);
  }

  /*news.php*/
  public function set_news($name, $txt, $user_id)
  {
    $sql="INSERT INTO news(id, user_id, name, text, date) VALUES (NULL, '$user_id', '$name', '$txt', CURRENT_TIMESTAMP)";
    echo $sql;
    return $this->query($sql);
  }

  public function upd_news($id, $name, $txt)
  {
    $sql="UPDATE news SET text = '$txt', name = '$name' WHERE id = '$id'";
    return $this->query($sql);
  }
  public function del_news($id)
  {
    $sql="DELETE FROM news WHERE id = '$id'";
    return $this->query($sql);
  }
  public function get_news($id)
  {
    $sql="SELECT * FROM news WHERE id = '$id'";
    return $this->query($sql);
  }
  public function get_n_news($count)
  {
    $sql="SELECT * FROM news ORDER BY date DESC LIMIT $count";
    return $this->query($sql);
  }
  public function get_all_news()
  {
    $sql="SELECT * FROM news ORDER BY date DESC";
    return $this->query($sql);
  }

  /*schedule.php*/
  public function upd_schedule($id, $week, $day, $pair, $type, $corps, $auditory, $subject_id, $group_id, $teacher_id)
  {
    $sql="UPDATE schedule SET week='$week', day='$day', pair='$pair', type='$type', corps='$corps', auditory='$auditory', subject_id='$subject_id', group_id='$group_id', teacher_id='$teacher_id' WHERE id = '$id'";
    return $this->query($sql);
  }  

  public function get_schedule($id)
  {
    $sql="SELECT * FROM schedule WHERE id = '$id'";
    return $this->query($sql);
  }

  public function del_schedule($id)
  {
    $sql="DELETE FROM schedule WHERE id = '$id'";
    return $this->query($sql);
  }

  public function set_new_schedule($week, $day, $pair, $type, $corps, $auditory, $subject_id, $group_id, $teacher_id)
  {
    $sql="INSERT INTO schedule(id, week, day, pair, type, corps, auditory, subject_id, group_id, teacher_id) VALUES (NULL, '$week', '$day', '$pair', '$type', '$corps', '$auditory', '$subject_id', '$group_id', '$teacher_id')";
    //echo $sql;
    return $this->query($sql);
  }

  public function get_subjects_for_group($group)
  {
    $sql="SELECT subjects.id as id1, subjects.* FROM subjects JOIN schedule on schedule.subject_id = subjects.id JOIN groups ON schedule.group_id = groups.id WHERE group_number = '$group' GROUP BY id1";
    return $this->query($sql);
  }

  public function get_subjects()
  {
    $sql="SELECT * FROM subjects";
    return $this->query($sql);
  }

  public function daily_g($day, $group_id, $ws)
  {
    $sql="SELECT schedule.id as id1, subjects.id as id2, schedule.*, groups.*, subjects.* FROM schedule JOIN groups ON schedule.group_id = groups.id JOIN subjects ON subjects.id=schedule.subject_id WHERE day = '$day' AND group_id = '$group_id' AND week IN $ws order by schedule.pair";
    return $this->query($sql);
  }

  public function weekly_g($group_id, $ws)
  {
    $sql="SELECT schedule.id as id1, subjects.id as id2, schedule.*, groups.*, subjects.* FROM schedule JOIN groups ON schedule.group_id = groups.id JOIN subjects ON subjects.id=schedule.subject_id WHERE group_id = '$group_id' AND week IN $ws order by schedule.day, schedule.pair";
    return $this->query($sql);
  }

  public function daily_t($day, $teacher_id, $ws)
  {
    $sql="SELECT schedule.id as id1, subjects.id as id2, schedule.*, groups.*, subjects.* FROM schedule JOIN groups ON schedule.group_id = groups.id JOIN subjects ON subjects.id=schedule.subject_id WHERE day = '$day' AND teacher_id = '$teacher_id' AND week IN $ws order by schedule.pair";
    return $this->query($sql);
  }

  public function weekly_t($teacher_id, $ws)
  {
    $sql="SELECT schedule.id as id1, subjects.id as id2, schedule.*, groups.*, subjects.* FROM schedule JOIN groups ON schedule.group_id = groups.id JOIN subjects ON subjects.id=schedule.subject_id WHERE teacher_id = '$teacher_id' AND week IN $ws order by schedule.pair";
    return $this->query($sql);
  }


  private function dbconnect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD)
    or die ("<br/>Could not connect to MySQL server");

    mysqli_select_db($conn, DB_NAME)
    or die ("<br/>Could not select the indicated database");

    return $conn;
  }

  private function query($sql){

    $link = $this->dbconnect();

    $res = mysqli_query($link,$sql);

    if ($res){
      if (strpos($sql,'SELECT') === false){
        return true;
      }
    }
    else{
      if (strpos($sql,'SELECT') === false){
        return false;
      }
      else{
        return null;
      }
    }

    $results = array();
    
    while ($row = mysqli_fetch_assoc($res)){
      $results[] = $row;
    }
    mysqli_close($link);
    return $results;      
  }  
}
?>