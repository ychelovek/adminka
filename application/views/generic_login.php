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
                <div class="span2" id="sidebar" style="margin-right: 20px;">
                    <ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
                    <li class="active"><a style="background: #808080;">Таблицы</a></li>
                    <?php if (isset($all_table)) { foreach ($all_table as $value): ?>
                        <li class="<?php if ($value == $current_menu) echo 'active' ?> ">
                            <a href="<?php echo url::base(); ?>pages/table/<?=$value; ?>"><i class="icon-chevron-right"></i>
                            <?php if ( !empty($crud_tables[$value]['name']) ) echo $crud_tables[$value]['name']; else echo $value; ?>
                            </a>
                        </li>
                    <?php endforeach; } ?>
                       <!--  <li class="active">
                            <a href="index.html"><i class="icon-chevron-right"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="calendar.html"><i class="icon-chevron-right"></i> Calendar</a>
                        </li>
                        <li>
                            <a href="stats.html"><i class="icon-chevron-right"></i> Statistics (Charts)</a>
                        </li>
                        <li>
                            <a href="form.html"><i class="icon-chevron-right"></i> Forms</a>
                        </li>
                        <li>
                            <a href="tables.html"><i class="icon-chevron-right"></i> Tables</a>
                        </li>
                        <li>
                            <a href="buttons.html"><i class="icon-chevron-right"></i> Buttons & Icons</a>
                        </li>
                        <li>
                            <a href="editors.html"><i class="icon-chevron-right"></i> WYSIWYG Editors</a>
                        </li>
                        <li>
                            <a href="interface.html"><i class="icon-chevron-right"></i> UI & Interface</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-success pull-right">731</span> Orders</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-success pull-right">812</span> Invoices</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-info pull-right">27</span> Clients</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-info pull-right">1,234</span> Users</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-info pull-right">2,221</span> Messages</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-info pull-right">11</span> Reports</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-important pull-right">83</span> Errors</a>
                        </li>
                        <li>
                            <a href="#"><span class="badge badge-warning pull-right">4,231</span> Logs</a>
                        </li> -->
                    </ul>
                </div>
                
                <!--/span-->


                  <?=$content ?>
            </div>
            <hr>
            <footer>
                <p>&copy; Vincent Gabriel 2013</p>
            </footer>
        </div>
        <!--/.fluid-container-->
        <!--<script src="<?php echo url::base(); ?>media/vendors/jquery-1.9.1.min.js"></script>-->
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
        <script>
    $(function() {
     $('.dtp').datetimepicker({
      language: 'ru'
    });

   // $('textarea').markItUp(mySettings);
   
    $("select.search").select2();
   
  });
        </script>
    </body>

</html>