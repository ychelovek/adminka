<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
@package    Core
@author     Kohana Team
@copyright  (c) 2007-2008 Kohana Team
@license    http://kohanaphp.com/license.html
*/



/**
Pages_Controller

@brief Основной контроллер.
Отвечает за просмотр/редактирование/удаление рзделов

В Pages_Controller реализованы все основные функции Админ-панели такие как:
@n-Вывод списка таблиц
@n-Удалиние строк из БД
@n-Просмотр таблиц
@n-Редактирование полей таблиц
*/
class Pages_Controller extends CRUD_Controller
{
	//const ALLOW_PRODUCTION = FALSE;

	/** Основной файл темы*/
	public $template = 'generic_login';


	public function index()
	{
	/**
		@brief Главная страница @b панели @b администратор
	*/
		
		//Выход
		$logout = $this->input->get('logout') ? TRUE : FALSE;
		if ($logout) {
			cookie::delete('id');
		}

		//Проверка авторизованности
		$this->_check_auth();

		$this->template->content = View::factory('inlogin');
		$this->template->all_table = $this->all_table;
		$this->template->crud_tables = $this->crud_tables;
		$this->template->login = $this->login;
		$this->template->current_menu = $this->current_menu;
	}


	public function delete($this_table, $id=0)
	{
		/**
		
		@brief Удаление из таблиц

		Данный метод вызывается из @ref edit и удаляет выбранную строку из выбранной таблицы

		@param this_table - название таблицы
		@param id - ID первичного ключа
		
		*/

		//Проверка авторизованности
		$this->_check_auth();

		$all_file_fields = array();
		$uploaddir = rtrim( $_SERVER['DOCUMENT_ROOT'], '/');
		$this_t = array();
		//Путь загрузки изображений, указывается в config/config_table
		$user_upload_dir = rtrim( $this->crud_config['filepath'], '/');
		$user_upload_dir = ltrim( $user_upload_dir, '/');
		foreach ($this->crud_tables[$this_table]['fields'] as $key => $value) {
			if ($this->crud_tables[$this_table]['fields'][$key]['type']=='file') {
				//Помещаем в массив все поля FILE из config_tables текущей таблицы
				$all_file_fields[] = '`'.$key.'`';
				$this_t[$key] = $this->crud_tables[$this_table]['fields'][$key];
			}
		}

		foreach ($this_t as $field => $params) {
			$type = $params['type'];
			if ( $type == 'file' ) {
				$arr_file_params[$field] = isset($params['params']) ? $params['params'] : NULL;
			}
		}

		$primary = $this->getPrimary($this_table);

		//Если есть поля-ФАЙЛЫ то получаем ссылки из БД...
		if (!empty($all_file_fields)) {
			$all_file_names = implode(',', $all_file_fields);
			$query_delete_files = "SELECT $all_file_names from `$this_table` WHERE `$primary` = '$id'";
			$sel_res = $this->db->query($query_delete_files)->current();

			if ($sel_res!==FALSE) {
				foreach ($sel_res as $key => $file) {
					$add_file_in_db_type = 0;
					$cur_upload_path = NULL;
							if ($arr_file_params[$key]!=NULL) {
								$this_file_params = explode('::', $arr_file_params[$key]);
								$add_file_in_db_type = isset($this_file_params[1]) ? $this_file_params[1] : 0;
								$cur_upload_path = $this_file_params[0];
								$cur_upload_path = rtrim( $cur_upload_path, '/');
								$cur_upload_path = ltrim( $cur_upload_path, '/');
							}
							switch ($add_file_in_db_type) {
								case '0':
									$add_url_del = NULL;
									break;

								case '1':
									$add_url_del = '/' . $user_upload_dir . '/';
									break;

								case '2':
									$add_url_del = '/' . $user_upload_dir . '/' . $cur_upload_path . '/';
									break;
								
								default:
									$add_url_del = NULL;
									break;
							}
					$del_file = $uploaddir . $add_url_del . $file;
					//И удаляем эти файлы
					if ( file_exists($del_file) && !empty($file) ) {
						unlink($del_file);
					}
				}
			}
		}
		//Удаляем строку из таблицы
		$query = "DELETE FROM `$this_table` WHERE `$primary` = '$id'";
		$this->db->query($query);


		$this->template->content = View::factory('delete');
		$this->template->content->message = 'Удалено!';
		$this->template->content->this_table = $this_table;
		$this->template->all_table = $this->all_table;
		$this->template->crud_tables = $this->crud_tables;
		$this->template->login = $this->login;
		$this->template->current_menu = $this->current_menu;
	}



	public function table($this_table = NULL)
	{
	/**
		
	@brief Данный метод реализует просмотр таблицы

	@param this_table - название таблицы
	
	*/
		$this->_check_auth();
		$this_key_name = array();
		$this_key = array();
		if ( ! empty($this_table) ) {
			if ( isset($this->crud_tables[$this_table]) ) {
				foreach ($this->crud_tables[$this_table]['fields'] as $key => $value) {
					//Если в конфиге указано 0 - то не выводим это поле при просмотре таблицы
					if (isset($this->crud_tables[$this_table]['fields'][$key]['visible'])) {
						if ($this->crud_tables[$this_table]['fields'][$key]['visible']==0) {
							continue;
						}
					}
					$this_t[$key] = $this->crud_tables[$this_table]['fields'][$key];
					//Массив названий всех полей
					$this_key[] = $key;
					$this_key_name[] = !empty($this->crud_tables[$this_table]['fields'][$key]['title']) ? $this->crud_tables[$this_table]['fields'][$key]['title'] : $key;
				}
			} else {
				header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/?err=0');
				exit;
			}
		} else {
			header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/?err=1');
			exit;
		}

		$this_table = $this->db->escape_str($this_table);

		//ЕСЛИ ЕСТЬ 'title' у таблицы в config_table то выводим его, если нет, то выводим название таблицы.
		$table_title = ( !empty($this->crud_tables[$this_table]['title']) ) ? $this->crud_tables[$this_table]['title'] : $this_table;

		$get_all_tables = $this->db->query("SHOW TABLES");
		foreach ($get_all_tables as $key => $table)  {
			foreach ($table as $name_table)  {
				$arr_tab[] = $name_table;
			}
		}

		//Если таблица не найдена в БД то exit
		if ( !in_array($this_table, $arr_tab) ) {
			header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/?err=1');
			exit;
		}

		// print_r($this_key);
		// die();
		foreach ($this_key as $key => $value) {
			//Получаем поля которые нам можно редактировать
			$this_key_edit[] = $this_table.'.'.$value;
		}


		//Получаем первичный ключ
		$primary = $this->getPrimary($this_table);

		$combine_query  = "SELECT {$primary} AS primary_key, ";
		$combine_query .= implode(', ', $this_key_edit);
		$combine_query .= " FROM `" . $this_table . "`";

		//Search
		$add_to_count = NULL;
		$field = $this->input->get('field');
		$query = $this->input->get('query');
		$strict = $this->input->get('strict');
		$query_where = array();

		if ( isset($field) and isset($query) ) {
			if ( isset($strict) ) {
				$query_where[] = " AND {$field} = '{$query}'";
			} else {
				$query_where[] = " AND {$field} LIKE '%{$query}%'";
			}
		}

		//Если перешли по связи
		$link = $this->input->get('link');
		if ( ! empty($link) ) {
			$rel_arr = explode(',', $link);
			foreach ($rel_arr as $key => $value) {
				$query_link = substr($value, strrpos($value, '-') + 1);
				$field_link = substr($value, 0, strrpos($value, '-'));
				$query_where[] = " AND `{$field_link}` = '{$query_link}'";
			}
		}
		$combine_query .= ' WHERE 1=1 '.implode(' ', $query_where);
		// echo $combine_query;

		$total_rows = $this->db->query("SELECT count(*) AS total FROM {$this_table} WHERE 1=1 ".implode(' ', $query_where)."")->current()->total;
		//Количество строк на странице
		$per_page = 30;
		$num_pages = ceil($total_rows/$per_page);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$offset = $per_page * ($page - 1);



		//СОРТИРОВКА
		$ordering = !empty($this->crud_tables[$this_table]['order']) ? $this->crud_tables[$this_table]['order'] : NULL;
		$ordering = !empty($ordering) ? explode('::', $ordering) : NULL;
		$order = isset($ordering[0]) ? $ordering[0] : $primary;
		$order_type = isset($ordering[1]) ? $ordering[1] : 'desc';

		$order_from_get = $this->input->get('sort');
		$order = !empty($order_from_get) ? $order_from_get : $order;

		$order_type_from_get = $this->input->get('sorttype');
		$order_type = !empty($order_type_from_get) ? $order_type_from_get : $order_type;
		$combine_query .= !empty($order) ? " ORDER BY `$order` $order_type" : '';
		//КОНЕЦ СОРТИРОВКИ
		$combine_query .= " LIMIT {$offset}, {$per_page}";

		//Build_query. Что бы при навигации не терялась сортировка
		$build_query = array();
		$build_query = $this->input->get();
			//echo $combine_query;
		// die();

		$query = $this->db->query($combine_query);
		if ($query===FALSE) {
			echo 'Ошибка запроса ~ ' . __LINE__ . ' строка';
			die();
		}

		$this->current_menu = $this_table;

		$this->template->content = View::factory('view_table');

		$this->template->current_menu = $this->current_menu;
		$this->template->all_table = $this->all_table;
		$this->template->crud_tables = $this->crud_tables;
		$this->template->login = $this->login;

		$this->template->content->tablica = $query->as_array();
		$this->template->content->keys = $this_key;
		$this->template->content->keys_name = $this_key_name;
		$this->template->content->this_table = $this_table;
		$this->template->content->table_title = $table_title;
		$this->template->content->this_t = $this_t;
		$this->template->content->primary = $primary;
		$this->template->content->num_pages = $num_pages;
		$this->template->content->page = $page;
		$this->template->content->build_query = $build_query;
		$this->template->content->order = $order;
		$this->template->content->order_type = $order_type;
		$this->template->content->link = $link;
	}



	public function edit($this_table, $id=0)
	{
		/**
		
		@brief Редактирование выбранной строки таблицы

		Данный метод выводит выбранную строку на редактирование

		@param this_table - название таблицы
		@param id - ID первичного ключа
		
		*/

		$this->_check_auth();	//Проверка авторизации

		$id = intval($id);
			$arr_type = array(
				'string' 	=> '<input %ATTR% type="text" style="width:95%" name="%NAME%" value="%VALUE%">',
				'int' 		=> '<input %ATTR% type="text" style="width:95%" name="%NAME%" value="%VALUE%" pattern="^[ 0-9]+$" required>',
				'double' 	=> '<input %ATTR% type="text" name="%NAME%" value="%VALUE%" pattern="\d+(\.\d+)?" required>',
				'file' 		=> '<input type="hidden" value="%VALUE%" name="%NAME%">
								<input %ATTR% type="file" name="%NAME%">
								<div id="error%NAME%"></div>',
				'date' 		=> '<div id="datetimepicker1" class="dtp input-append date">
								<input %ATTR% type="text" data-format="%OPTION%" name="%NAME%" value="%VALUE%"><span class="add-on">
								<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>',   
				'text' 		=> '<textarea %ATTR% name="%NAME%" class="texts %CLASS%" id="%NAME%">%VALUE%</textarea><br>',
				'checklist' => '<label class="checkbox span3">
								<input %ATTR% type="checkbox" name="%NAME%[]" value="%VALUE%">%OPTION%
								</label>',
				'checkbox'	=> '<label class="checkbox span9"><input %ATTR% type="checkbox" name="%NAME%" value="%VALUE%">Да</label>',
				'select'	=> '<option value="%VALUE%" %ATTR%>%OPTION%</option>'
			);
		$success = NULL;

		$uploaddir = rtrim( $_SERVER['DOCUMENT_ROOT'], '/');
		//Путь загрузки изображений, указывается в config/config_table
		$user_upload_dir = rtrim( $this->crud_config['filepath'], '/');
		$user_upload_dir = ltrim( $user_upload_dir, '/');


		$this_table = $this->db->escape_str($this_table);

		//ЕСЛИ ЕСТЬ 'title' у таблицы в config_table то выводим его, если нет, то выводим название таблицы.
		$table_title = ( !empty($this->crud_tables[$this_table]['title']) ) ? $this->crud_tables[$this_table]['title'] : $this_table;
		if ( (isset($this_table)) && (!empty($this_table)) ) {
			if ( (isset($this->crud_tables[$this_table]['fields'])) && (!empty($this->crud_tables[$this_table]['fields']) ) ) {
				foreach ($this->crud_tables[$this_table]['fields'] as $key => $value) {
					$this_t[$key] = $this->crud_tables[$this_table]['fields'][$key];
					$this_key[] = $key;
					$all_type_this_table[] = ($this->crud_tables[$this_table]['fields'][$key]['type']);
				}
			} else {
				header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/?err=0');
				exit;
			}
		} else {
			header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/?err=1');
			exit;
		}


		foreach ($this_key as $key => $value) {
			$this_key_edit[] = $this_table.'.'.$value;
		}

		//Получаем первичный ключ
		$primary = $this->getPrimary($this_table);
		$link = $this->input->get('link');
		///////////////
		//UPDATE TABLES
		////////
		if ($this->input->post()) {

			$all_post = $this->input->post();
			// print_r($all_post);
			// die();

			$arr_fields_name 	= array();
			$arr_fields_value 	= array();
			$arr_fields_value2 	= array();
			$arr_file_extras 	= array();
			$arr_file_params 	= array();
			$arr_success_files 	= array();
			$err_arr 			= array();

			foreach ($this_t as $field => $params) {
				$value = isset($all_post[$field]) ? $all_post[$field] : NULL;
				$type = $params['type'];

				if ( $type == 'file' ) {
					$arr_file_extras[$field] = $params['extras'];
					$arr_file_params[$field] = isset($params['params']) ? $params['params'] : NULL;
				}
				if ( $type == 'checklist' ) {
					if (is_array($value))	$value = implode(':', $value);
				}
				if ( $type == 'checkbox' ) {
					$value = isset($all_post[$field]) ? $all_post[$field] : 0;
				}
				if ( $type != 'files' ) {
					if ( array_key_exists($field, $all_post) ) {
						$arr_fields_name[] = "`".$field."`";
						$arr_fields_value[$field] = "'".$this->db->escape_str($value). "'";
					}
				}
			}


			//ЕСЛИ ФОРМА С ФАЙЛАМИ
			if (!empty($_FILES)) {
				//МАССИВ С MIME типами
				$loc_mime_types = array
				(
					"application"   => array("exe", "msi", "bat", "bin", "com", "scr"),
					"audio"         => array("mp3", "ogg", "wav", "wma", "aiff", "aif", "mid"),
					"document"      => array("doc", "docx", "pdf", "chm", "hlp", "rtf"),
					"flash"         => array("swf", "fla"),
					"font"          => array("fon", "ttf", "otf"),
					"html"          => array("htm", "html", "shtm", "shtml", "dhtm", "shtml", "xml"),
					"image"         => array("bmp", "jpg", "jpeg", "gif", "png", "tiff", "pic", "tga", "pcx"),
					"package"       => array("rar", "zip", "7z", "tgz", "gz", "tar"),
					"presentation"  => array("ppt"),
					"script"        => array("vbs", "js", "php", "inc", "css"),
					"spreadsheet"   => array("xls"),
					"text"          => array("txt", "nfo", "inf", "ion", "dis", "diz"),
					"video"         => array("avi", "wmv", "mov", "flv", "mp4", "vob", "3gp", "mpg")
				);

				//ПАТТЕРН ДЛЯ ТИПОВ ФАЙЛОВ
				$shablons = array(
					'^(\*\/\*)$' => '*',
					'^[a-z]+(\/\*)$' => 'all_in_one',
					'^[a-z]+$' => 'one_in_one'
				);

				foreach ($arr_file_extras as $name => $params) {
					$get_type = array();
					$arr_file_types = array();
					$arr_file_ext = array();
					$success_types = FALSE;

					$params = explode('::', $params);

					//ЕСЛИ у поля таблицы НЕ УКАЗАН МАКСИМАЛЬНЫЙ РАЗМЕР ФАЙЛА В config_table то берем из crud-контроллера
					$size	= isset($params[1]) ? intval($params[1])*1024 : $this->max_file_size;

					//ПРОВЕРИТЬ КАК СЛЕДУЕТ $params дубликат
					$required_type = $params[0];
					$required_type = explode(':', $required_type);

					//несколько разных типов, или 1
					if ( count($required_type)==1 ) {
						foreach ($shablons as $pattern => $execute) {
							//Если подходит по шаблону $shablons
							if ( preg_match( "~$pattern~", $required_type[0]) ) {
								if ( $shablons[$pattern] == 'all_in_one' ) {
									$types = explode('/', $required_type[0]);
									$get_type = $loc_mime_types[$types[0]];
								} elseif ( $shablons[$pattern] == 'one_in_one') {
									$get_type[] = $required_type[0];
								} elseif ( $shablons[$pattern] == '*') {
									$success_types = TRUE;
								}
								break;
							}
						}
					} else {
						$type_arr = array();
						foreach ($required_type as $key => $value) {
							foreach ($shablons as $pattern => $execute) {
								if ( preg_match( "~$pattern~", $required_type[$key]) ) {
									if ( $shablons[$pattern] == 'all_in_one' ) {
										$types = explode('/', $required_type[$key]);
										$get_type = array_merge($get_type, $loc_mime_types[$types[0]]);
									} elseif ( $shablons[$pattern] == 'one_in_one' ) {
										// $get_type = explode('/', $required_type[$key]);
										// $type_arr[] = $get_type[0];
										// $arr_file_ext[] = $get_type[1];
										$get_type[] = $required_type[$key];
									}
									break;
								}
							}
						}
					}



					//ЕСЛИ ПОЛЬЗОВАТЕЛЬ ВЫБРАЛ ФАЙЛ
					if ( isset($_FILES[$name]) ) {
						if ( $_FILES[$name]['error']==0 ) {

							//Проверяем размер файла
							if ( ($size==0) || ($size>$_FILES[$name]['size']) ) {
								if ( !$success_types ) {
									$file_type = $_FILES[$name]['type'];
									$file_name = $_FILES[$name]['name'];

									//Получаем расширение файла
									$extens = mb_strtolower(substr(strrchr($file_name, '.'), 1));
									if ( in_array($extens, $get_type) ) {
										$success_types = TRUE;
									}
									if ( !$success_types ) {
										foreach ($arr_file_types as $k => $type) {
											if (in_array($extens, $loc_mime_types[$type]))
											{
												$success_types = TRUE;
											}
										}
									}
								}


								//Проверяем разрешен ли тип выбранного файла
								if ($success_types) {
									if ( empty($err_arr) ) {
										$md5_flie = md5(microtime()) . $_FILES[$name]['name'];

										/*
										Если в config_tables указан 5 параметр (дополнительный путь:тип_добавления_в_бд)
										*/
										//ТИП ЗАГРУЗКИ ФАЙЛА В БАЗУ
										$add_file_in_db_type = 0;
										$cur_upload_path = NULL;
										if ($arr_file_params[$name]!=NULL) {
											$this_file_params = explode('::', $arr_file_params[$name]);
											$add_file_in_db_type = isset($this_file_params[1]) ? $this_file_params[1] : 0;
											$cur_upload_path = $this_file_params[0];
											$cur_upload_path = rtrim( $cur_upload_path, '/');
											$cur_upload_path = ltrim( $cur_upload_path, '/');
										}

										//Устанавливаем пути загрузки файлов
										$uploadfile = $cur_upload_path==NULL ? $uploaddir . '/' . $user_upload_dir . '/'. $md5_flie : 
										$uploaddir . '/' . $user_upload_dir . '/' . $cur_upload_path . '/'. $md5_flie;

										//Путь загрузки файлов.
										$dirk 		= $cur_upload_path==NULL ? $uploaddir . '/' . $user_upload_dir : 
										$uploaddir . '/' . $user_upload_dir . '/' . $cur_upload_path;

										//Устанавливаем пути которые будут храниться в БД
										switch ($add_file_in_db_type) {
											case '0':
												$name_file  = $cur_upload_path==NULL ?  '/' . $user_upload_dir . '/' . $md5_flie : 
												'/' . $user_upload_dir . '/' . $cur_upload_path . '/' . $md5_flie;
												$add_url_del = NULL;
												break;

											case '1':
												$name_file  = '/' . $cur_upload_path . '/' . $md5_flie;
												$add_url_del = '/' . $user_upload_dir . '/';
												break;

											case '2':
												$name_file  = $md5_flie;
												$add_url_del = '/' . $user_upload_dir . '/' . $cur_upload_path . '/';
												break;
											
											default:
												$name_file  = $cur_upload_path==NULL ?  '/' . $user_upload_dir . '/' . $md5_flie : 
												'/' . $user_upload_dir . '/' . $cur_upload_path . '/' . $md5_flie;
												$add_url_del = NULL;
												break;
										}

										// echo 'В БД ' . $name_file;
										// echo '<br>';
										// echo 'Для заливки ' . $uploadfile;
										// die();

										// $uploadfile = $uploaddir . $user_upload_dir . '/'. $md5_flie;
										// $name_file  = $user_upload_dir . '/'. $md5_flie;
										// $dirk 		= $uploaddir . $user_upload_dir;

										if( !is_dir($dirk) ) mkdir($dirk);
										if ( move_uploaded_file($_FILES[$name]['tmp_name'], $uploadfile) ) {
											if ( !empty($all_post[$name]) ) {
												//Если в поле уже был файл - удаляем его
												if ( file_exists($uploaddir . $add_url_del . $all_post[$name]) ) {
													//unlink($_SERVER['DOCUMENT_ROOT'] . $all_post[$name]);
													unlink($uploaddir . $add_url_del . $all_post[$name]);
												}
											}
											//echo $name."Файл загружен.\n";
											//$err_arr[$name] = 'Файл загружен!';
											$arr_success_names[] = '`'.$name.'`';
											$arr_success_files[] = "'".$uploadfile."'";
											$arr_fields_value[$name]   = "'".$name_file."'";
										} else {
											echo 'errrrrr';
										}
									}
								} else {
									//echo $name.'Тип запрещен';
									$err_arr[$name] = 'Тип запрещен';
								}
							} else {
								//echo 'Превышен размер файла';
								$err_arr[$name] = 'Превышен размер файла';
							}
						}
					}
				}

				if ( empty($err_arr) ) {
					//Удаляем файл если стоит галочка
					if ( isset($all_post['delete_file']) ) {
						foreach ($all_post['delete_file'] as $name_del_field) {
							$add_file_in_db_type = 0;
							$cur_upload_path = NULL;
							if ($arr_file_params[$name_del_field]!=NULL) {
								$this_file_params = explode('::', $arr_file_params[$name_del_field]);
								$add_file_in_db_type = isset($this_file_params[1]) ? $this_file_params[1] : 0;
								$cur_upload_path = $this_file_params[0];
								$cur_upload_path = rtrim( $cur_upload_path, '/');
								$cur_upload_path = ltrim( $cur_upload_path, '/');
							}
							switch ($add_file_in_db_type) {
								case '0':
									$add_url_del = NULL;
									break;

								case '1':
									$add_url_del = '/' . $user_upload_dir;
									break;

								case '2':
									$add_url_del = '/' . $user_upload_dir . '/' . $cur_upload_path . '/';
									break;
								
								default:
									$add_url_del = NULL;
									break;
							}
							$del_file = $uploaddir . $add_url_del . str_replace("'", "", $arr_fields_value[$name_del_field]);
							//echo $name_del_field;
							if ($_FILES[$name_del_field]['error']!=0) {
								if ( file_exists($del_file) ) {
									unlink($del_file);
								}
								//Если удаляем файл, то делаем пустое поле ''
								$arr_fields_value[$name_del_field] = "''";
							}
						}
					}
				}
			}
				if ( empty($err_arr) ) {

					if ($id==0) {
						$query_update = "INSERT INTO `$this_table` (";
						$query_update .= implode(', ', $arr_fields_name);
						$query_update .= ") VALUES (";
						$query_update .= implode(', ', $arr_fields_value) . ")";
					} else {
						$update_set = '';
						foreach ($arr_fields_value as $kei => $znach) {
							$update_set .= "`$kei`= $znach,";
						}
						$update_set = substr($update_set, 0, -1);
						$query_update = "UPDATE `$this_table` SET ";
						$query_update .=$update_set;
						$query_update .=" WHERE `$primary`='$id'";
					}

					$res = $this->db->query($query_update);
					if ($res!==FALSE) {
						//echo 'Успешно добавлено';
						//$err_arr['all_info'] = 'Изменения сохранены';
						$success = 'Изменения сохранены';
						if ($id==0 || !empty($link) ) {
							// //Редирект на добавленную запись
							// header('Location: http://' . $_SERVER['SERVER_NAME'] . url::base() . 'pages/edit/' . $this_table . '/' . mysql_insert_id());
							if ( ! empty($link) ) {
								header('Location: http://' . $_SERVER['SERVER_NAME'] . url::base() . 'pages/table/' . $this_table . '?link=' . $link);
							} else {
							header('Location: http://' . $_SERVER['SERVER_NAME'] . url::base() . 'pages/table/' . $this_table);
							}
							exit();
						}
					}
				}
		}

		//////UPDATE END


	$editing_table = $this->crud_tables[$this_table]['fields'];
	$relations = !empty($this->crud_tables[$this_table]['relations']) ? $this->crud_tables[$this_table]['relations'] : NULL;

	$types = array();
	foreach ($editing_table as $key => $value) {
		//Данные config_table заносим в массивы
		$types[$key] 	= $value['type'];
		$defaults[$key] = !empty($value['default']) ? $value['default'] : '';
		$labels[$key] 	= !empty($value['title'])   ? $value['title'] : $key;
		$enabled[$key] 	= isset($value['enabled'])   ? $value['enabled'] : 1;
		$extrases[$key] = !empty($value['extras'])  ? $value['extras'] : '';
		$params[$key] 	= !empty($value['params'])  ? $value['params'] : NULL;
		$classes[$key] 	= !empty($value['class'])  ? $value['class'] : NULL;
	}



	$relations_pages 	= array();
	$relations_titles 	= array();
	$query_links 		= array();
	$field_links 		= array();
	//ВЫВОД ДЛЯ РЕДАКТИРОВАНИЯ
	if($id!=0)
	{
		$combine_query  = "SELECT ";
		$combine_query .= implode(', ', $this_key_edit);
		$combine_query .= " FROM `{$this_table}` ";
		$combine_query .= " WHERE `$primary` = '$id' ";
		$query = $this->db->query($combine_query);
		$tablica = (array)$query->current();
		//Std to Array
		$tablica = (array)$tablica;

		if ($relations!=NULL) {
			foreach ($relations as $key => $value) {
				$relations[$key] = explode(':', $value);
				$relation_link = str_replace('%ID%', $id, $relations[$key][2]);
				$relations_pages[] = url::base() . 'pages/table/' . $relations[$key][1] . '?link=' . $relation_link;
				$relations_titles[] = $relations[$key][0];
			}
		}
	}
	else
	{
		//Если перешли по связи
		$link = $this->input->get('link');
		if ( ! empty($link) ) {
			$rel_arr = explode(',', $link);
			foreach ($rel_arr as $key => $value) {
				$query_links[] = substr($value, strrpos($value, '-') + 1);
				$field_links[] = substr($value, 0, strrpos($value, '-'));
			}
		}

		foreach ($types as $name => $type) {
			$tablica[$name] = '';
			if (in_array($name, $field_links) ) {
				$tablica[$name] = $query_links[array_search($name, $field_links)];
			}
		}
	}

	//Тут будут храниться готовые строки
	$arr_replace_value = array();

	foreach ($tablica as $field_name => $field_value) {
		$arr_def_ext 	= array();
		$def_ext  		= NULL;
		$type 			= $types[$field_name];
		$extras 		= $extrases[$field_name];
		$default 		= $defaults[$field_name];
		$this_pattern 	= $arr_type[$type];
		$param 			= $params[$field_name];
		$class 			= $classes[$field_name];

		if ($type=='date') {
			$this_pattern = str_replace('%OPTION%', $extras, $this_pattern);
		}

		if ($type=='checkbox') {
			$check = $field_value==1  ? 'checked' : '';
			if ($id==0) {
			$check = $default==1  ? 'checked' : '';
			}
				$this_pattern = str_replace('%VALUE%', '1' , $this_pattern);
				$this_pattern = str_replace('%ATTR%', $check, $this_pattern);
		}

		if ($type=='file') {
			if($field_value!=NULL) {
				if ($param!=NULL) {
					$this_file_params = explode('::', $param);
					$add_file_in_db_type = isset($this_file_params[1]) ? $this_file_params[1] : 0;
					$cur_upload_path = $this_file_params[0];
					$cur_upload_path = rtrim( $cur_upload_path, '/');
					$cur_upload_path = ltrim( $cur_upload_path, '/');

					//Путь загрузки файлов.
					$dirk = $cur_upload_path==NULL ? $uploaddir . '/' . $user_upload_dir : 
					$uploaddir . '/' . $user_upload_dir . '/' . $cur_upload_path;
					$add_url_file = NULL;
					//Устанавливаем пути которые будут храниться в БД
					switch ($add_file_in_db_type) {
						case '0':
							$add_url_file = NULL;
							break;

						case '1':
							$add_url_file = '/' . $user_upload_dir;
							break;

						case '2':
							$add_url_file = '/' . $user_upload_dir . '/' . $cur_upload_path . '/';
							break;
						
						default:
							$add_url_file = NULL;
							break;
					}
				}
				$real_file_path 	= $add_url_file . $field_value;
				$server_file_path 	= $uploaddir . $add_url_file . $field_value;
				$file_size 		= file_exists($server_file_path) ? round( filesize($server_file_path) / 1024, 2) . ' Кб.' : NULL;
				$this_pattern .='<label class="checkbox">
			    <input type="checkbox" name="delete_file[]" value="'.$field_name.'">Удалить
			    <a href="' . $real_file_path . '" target="_blank">Файл загружен</a> ' . $file_size . '</label>';
			}
			$this_pattern .="<br><br>";
		}

		if ($type=='select') {

			$start_select = '<select class="span7 ' . $class . '" name="%NAME%">';
			$start_select .= '<option value=""> - </option>';

			if ( !empty($default) ) {
				$default = explode('::', $default);
				for ($i=0; $i < count($default); $i++ ) {
					$arr_def_ext[] = explode('->', $default[$i]);
				}
				$extras[1] = 0;
				$extras[2] = 1;
				//print_r($arr_def_ext);
				
			} else {
				$extras = explode('::', $extras);
				$combine_q = "SELECT ";
				$combine_q .= ($extras[1]==$extras[2]) ? '`' . $extras[1] . '`' : '`' . $extras[1] . '`' . ', ' . '`' . $extras[2] . '`';
				$combine_q .= " FROM `{$extras[0]}`";
				$arr_def_ext = $this->db->query($combine_q);
				$arr_def_ext = $arr_def_ext->as_array();
				$arr_def_ext = $this->objectToArray($arr_def_ext);
			}

			
				foreach ($arr_def_ext as $key => $arr_values) {
					$def_ext = str_replace('%VALUE%', $arr_def_ext[$key][$extras[1]], $this_pattern);
					$def_ext = str_replace('%OPTION%', $arr_def_ext[$key][$extras[2]], $def_ext);
					if ( $field_value==$arr_def_ext[$key][$extras[1]] ) {
						$def_ext = str_replace('%ATTR%', 'selected', $def_ext);
					} else {
						$def_ext = str_replace('%ATTR%', '', $def_ext);
					}

					$start_select .= $def_ext;
				}



			$start_select .= '</select><div style="clear: both;">&nbsp;</div>';
			$this_pattern = $start_select;
		}



		if ($type=='checklist') {

			$start_checklist = '<div class="span12" style="height:0px; width:0;"></div>';
			//Превращаем в массив и удаляем пустые элементы
			$field_value = explode(':', $field_value);
			foreach ($field_value as $k => $val) {
				if (empty($val)) {
					unset($field_value[$k]);
				}
			}
			if ( !empty($default) ) {
				$default = explode('::', $default);
				for ($i=0; $i < count($default); $i++ ) {
					$arr_def_ext[] = explode('->', $default[$i]);
				}
				$extras[1] = 0;
				$extras[2] = 1;
				//print_r($arr_def_ext);
				
			} else {
				
				$extras = explode('::', $extras);
				$combine_q = "SELECT ";
				$combine_q .= ($extras[1]==$extras[2]) ? '`' . $extras[1] . '`' : '`' . $extras[1] . '`' . ', ' . '`' . $extras[2] . '`';
				//$combine_q .= " FROM `{$extras[0]}`";
				$combine_q .= " FROM `{$extras[0]}` ORDER BY BINARY(lower(`{$extras[2]}`))";
				$arr_def_ext = $this->db->query($combine_q);
				$arr_def_ext = $arr_def_ext->as_array();
				$arr_def_ext = $this->objectToArray($arr_def_ext);
			}

				foreach ($arr_def_ext as $key => $arr_values) {
					$def_ext = str_replace('%VALUE%', $arr_def_ext[$key][$extras[1]], $this_pattern);
					$def_ext = str_replace('%OPTION%', $arr_def_ext[$key][$extras[2]], $def_ext);
					$def_ext = str_replace('%NAME%', $field_name, $def_ext);
					if ( in_array($arr_def_ext[$key][$extras[1]], $field_value ) ) {
						$def_ext = str_replace('%ATTR%', 'checked', $def_ext);
					} else {
						$def_ext = str_replace('%ATTR%', '', $def_ext);
					}

					$start_checklist .= $def_ext;
				}



			$start_checklist .= '';
			$this_pattern = $start_checklist;
			//echo $this_pattern;
			// print_r($this_pattern);
			// die();
		}


		$this_pattern = str_replace('%NAME%', $field_name, $this_pattern);
		$this_pattern = str_replace('%CLASS%', $class, $this_pattern);
		$this_pattern = !is_array( $field_value ) ? str_replace('%VALUE%', $field_value, $this_pattern) : $this_pattern;
		$this_pattern = ($field_name == $primary) ? str_replace('%ATTR%', ' disabled', $this_pattern) : $this_pattern; //ЗАПРЕЩАЕМ РЕДАКТИРОВАНИЕ PRIMARY
		$this_pattern = str_replace('%ATTR%', '', $this_pattern);
		$arr_replace_value[$field_name] = $this_pattern;
	}




			if ( !empty($err_arr) ) {
				foreach ($err_arr as $err_field => $val_field) {
					$arr_replace_value[$err_field] = str_replace('<div id="error' . $err_field. '"></div>', '<div id="error'.$err_field.'" class="text-error">' . $val_field . '</div>', $arr_replace_value[$err_field]);
					//$results = str_replace('<div id="error' . $err_field. '"></div>', '<div id="error'.$err_field.'" class="error">' . $val_field . '</div>', $results);
				}
			}
			$this->current_menu = $this_table;


			$this->template->content = View::factory('edit_table');
			$this->template->all_table = $this->all_table;
			$this->template->crud_tables = $this->crud_tables;
			$this->template->login = $this->login;
			$this->template->current_menu = $this->current_menu;

			$this->template->content->keys = $all_type_this_table;
			$this->template->content->this_table = $this_table;
			$this->template->content->table_title = $table_title;
			$this->template->content->this_id = $id;
			$this->template->content->primary = $primary;
			$this->template->content->labels = $labels;
			$this->template->content->enabled = $enabled;
			$this->template->content->values = $arr_replace_value;
			$this->template->content->relations_pages = $relations_pages;
			$this->template->content->relations_titles = $relations_titles;
			$this->template->content->types = $types;
			$this->template->content->success = $success;

			//$this->template->content->edit_table = $results;
	}







	/*
	*
	*Дополнительные функции
	*
	*/
	function getPrimary( $this_table )
	{
		/**
		@brief Получаем первичный ключ

		@param this_table - таблица у которой получаем первичный ключ
		*/
		return $this->db->query("SHOW COLUMNS FROM `$this_table` WHERE `key`='pri'")->current()->Field;
	}




	function objectToArray($obj) {
	/**
	@brief Конвертирует @b stdClass @b Object в массив
	*/
		$rc = (array)$obj;
		foreach($rc as $key => &$field){
			if(is_object($field))$field = $this->objectToArray($field);
		}
		return $rc;
	}











		public function __call($method, $arguments)
	{
	/**

	@brief Функиця исключений.

	Если запрашиваемая функция не найдена, то будет вызвана @b _call функия. 
	
	@param method - название невыполненой функции
	@param arguments - аргументы невыполненой функции
	*/
		header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base() . 'error/');
		exit;

		$this->auto_render = FALSE;
		echo __('This text is generated by __call. If you expected the index page, you need to use: :uri:',
				array(':uri:' => 'welcome/index/'.substr(Router::$current_uri, 8)));
	}

}
