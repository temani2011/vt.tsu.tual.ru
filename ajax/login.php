<?php
	//$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1); - поддержка типов таблиц
	// localhost - unix, 127.0.0.1 - TCP/IP
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT'] . '/class/db.php');
	$dal = new DAL();
	if(isset($_POST["email"]))
	{ 
		$email=htmlspecialchars($_POST["email"]);
		$password=htmlspecialchars($_POST["password"]);
		$password=md5 ($password);
		$res = array();
		$results;
		//echo $_SERVER['HTTP_USER_AGENT'];
		//echo $_SESSION['token'];
		if(isset($_SESSION['token']))
		{
			if($results = $dal->get_token($_SESSION['token']))
			{
				if($results[0]['info'] == $_SERVER['HTTP_USER_AGENT']){
					echo json_encode($_SESSION['role']);
					exit();
				}
			}
		}
		if(isset($_POST["password"]))
		{
			$results = $dal->get_user_information($email,$password);
			if(empty($results))
			{
				echo json_encode(0);
			}
			else
			{
				$_SESSION["id"] = $results[0]["id"];
				$_SESSION["surname"] = $results[0]["surname"];
				$_SESSION["name"] = $results[0]["name"];
				$_SESSION["midname"] = $results[0]["midname"];
				$_SESSION["phone"] = $results[0]["phone"];
				$_SESSION["email"] = $results[0]["email"];
				$_SESSION["city"] = $results[0]["city"];
				$_SESSION["date_of_registration"] = $results[0]["date_of_registration"];
				$_SESSION["role"] = $results[0]["role"];
				$_SESSION["id_subfaculty"] = $results[0]["id_subfaculty"];
				$_SESSION["id_groups"] = $results[0]["id_groups"];
				$salt = md5($_SESSION["email"] . $results[0]["passwords"]);
				$dal->set_token($salt, $_SERVER['HTTP_USER_AGENT'], $_SESSION["id"]);
				$_SESSION["token"] = $salt;
				echo json_encode($_SESSION["role"]);
			}	
		}
	}
	else
	{
		$ans=$dal->del_token($_SESSION['token']);
		echo $ans;
		session_unset();
		session_destroy();
	}
 ?>
