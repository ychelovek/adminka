<!DOCTYPE html>
<html>
  <head>
    <title>Вход в панель управления</title>
    <!-- Bootstrap -->
    <link href="media/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="media/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="media/css/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="media/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>
  <body id="login">
    <div class="container">

      <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Вход</h2>
        <input type="text" class="input-block-level" id="login" name="login" placeholder="Логин">
        <input type="password" class="input-block-level" id="password" name="password" placeholder="Пароль">
         <? if ($err) echo '<div class="alert alert-danger">'.$err . '</div>'; ?>
         <!-- <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me     
         
        </label> -->
        <button class="btn btn-large btn-primary" type="submit">Вход</button>
      </form>

    </div> <!-- /container -->
    <script src="media/vendors/jquery-1.9.1.min.js"></script>
    <script src="media/js/bootstrap.min.js"></script>
  </body>
</html>