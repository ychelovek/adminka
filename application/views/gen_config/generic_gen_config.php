<!DOCTYPE html>
<html class="no-js">
    
    <head>
        <title><?php if (isset($title)) echo $title; else echo "Панель администратора"; ?></title>
        <!-- Bootstrap -->
        <link href="<?php echo url::base(); ?>media/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="<?php echo url::base(); ?>media/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="<?php echo url::base(); ?>media/vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
        <link href="<?php echo url::base(); ?>media/css/styles.css" rel="stylesheet" media="screen">
        <link href="<?php echo url::base(); ?>media/vendors/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

        <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
        <!-- EDITOR -->
        <script src="<?php echo url::base(); ?>media/editor/js/tinymce.min.js"></script>

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="media/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo url::base(); ?>">Панель администратора</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> <?php if (isset($login)) echo $login; else echo "NOT-AUTHORIZED"; ?> <i class="caret"></i>
                                </a>
                                <ul class="dropdown-menu">
                                <li>
                                        <a tabindex="-1" href="<?php echo url::base(); ?>pass/">Изменить пароль</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo url::base(); ?>pages?logout=1">Выход</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav">
                            <li class="">
                                <a href="<?php echo url::base(); ?>">Главная</a>
                            </li>
                            <li class="dropdown">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Настройки <b class="caret"></b>

                                </a>
                                <ul class="dropdown-menu" id="menu1">
                                    <li>
                                        <a href="#">Общие <i class="icon-arrow-right"></i>

                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">Настройки SEO</a>
                                    </li>                                 
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Разделы <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?php echo url::base(); ?>pages/news/">Новости</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo url::base(); ?>pages/cats/">Категории</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="<?php echo url::base(); ?>pages/static">Статические страницы</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="<?php echo url::base(); ?>pages/users/" role="button">Пользователи</a>
                            </li>
                            <li class="dropdown">
                                <a href="<?php echo url::base(); ?>gen_config" role="button">Генератор конфига</a>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">

<div class="row-fluid">
<?php echo $content; ?>
</div>
            </div>
            <hr>
            <footer>
                <p>&copy; Vincent Gabriel 2013</p>
            </footer>
        </div>
        <script src="<?php echo url::base(); ?>media/js/bootstrap.min.js"></script>
        <script src="<?php echo url::base(); ?>media/vendors/easypiechart/jquery.easy-pie-chart.js"></script>
        <script src="<?php echo url::base(); ?>media/js/scripts.js"></script>
        <script>
        $(function() {
            // Easy pie charts
            $('.chart').easyPieChart({animate: 1000});
        });
        </script>

         <script src="<?php echo url::base(); ?>media/vendors/datatables/js/jquery.dataTables.js"></script>
        <script src="<?php echo url::base(); ?>media/assets/DT_bootstrap.js"></script>
         <script src="<?php echo url::base(); ?>media/vendors/bootstrap-datetimepicker.min.js"></script>

        <link href="<?php echo url::base(); ?>media/vendors/select2.min.css" rel="stylesheet" />
        <link href="<?php echo url::base(); ?>media/vendors/select2-bootstrap.css" rel="stylesheet" />
		<script src="<?php echo url::base(); ?>media/vendors/select2.min.js"></script>
    </body>

</html>