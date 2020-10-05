<!DOCTYPE html>
<html lang="en">
<head>
	<title> Авторизация </title>
	<!-- Required meta tags -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS -->
  
  <!--<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">-->
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/index.css">
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->

  <!--<script src="https://code.jquery.com/jquery-3.2.1.min.js" ></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" ></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>-->
  <script src="/js/jquery-3.2.1.min.js" ></script>
  <script src="/js/popper.min.js" ></script>
  <script src="/js/bootstrap.min.js"></script>
  <!--Отправка данных login.php post-->
  <script>
    $(document).ready(function() {
      $("#unud").click(function() {
        $.post('ajax/login.php', {email: $("#inputEmail").val(), password: $("#inputPassword").val()}, function(data) {
          console.log(data);
          data = JSON.parse(data);
          if(data)
          {
            if(data != "staff" && data != "admin")
              window.location = "/"+data+"s/#news.php";
            else window.location = "/staff/#news.php";
          }
          else
            alert("Wrong password!");    
        })
      });
    });
    $(function(){
      $("#header").load("/view/header.php #header"); 
    });
    $(function(){
      $("#footer").load("/view/footer.php #footer");
    });
  </script>
</head>
<body>
  <!--Навигационная панель-->
  <div id="header"></div>
  <!--Тело с формой авторизации-->
  <div class = "container-fluid bg-transparent" id = "main-container">
    <div class="container">
     <div class="row justify-content-center">
      <div class="col-md-6">
       <div class="login-panel card">
        <div class="card-header cardA">
          <h3 class="card-title cardTitleA">Авторизация</h3>
        </div>
        <div class="card-block blockA">
          <form role="form">
            <fieldset>
              <div class="form-group">
                <i class="fa fa-user-o"></i>
                <input class="form-control" placeholder="Email" id="inputEmail" type="email" autofocus="">
              </div>
              <div class="form-group">
               <i class="fa fa-key"></i>
               <input class="form-control" placeholder="Пароль" id="inputPassword" type="password" value="">
             </div>
             <div class="checkbox">
              <label>
                <input name="remember" type="checkbox" value="Remember Me"> Запомнить
              </label>
            </div>
            <a id="unud" href="#" class="btn btn-primary">Войти</a>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!--Подвал-->
<div id="footer"></div>
</body>
</html>