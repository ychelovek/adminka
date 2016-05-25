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
class Gen_config_Controller extends CRUD_Controller
{
	//const ALLOW_PRODUCTION = FALSE;

	/** Основной файл темы*/
	public $template = 'gen_config/generic_gen_config';


	public function index()
	{

	/**
		@brief Генерация @bconfig_table@b
	*/



		$all_tables = $this->db->query("SHOW TABLES");

			//Все таблицы текущий БД
			$arrRealTables = array();

			foreach ($all_tables as $key => $tbl) {
				$row = array(current($tbl)=>'');
				$this_columns = $this->db->query('SHOW COLUMNS FROM `' . current($tbl) . '`');
				foreach ($this_columns as $key => $value) {
					$arrRealTables[current($tbl)][] = current($value);
				}
				unset($this_columns);
			}

// echo '<pre>';
// print_r($arrRealTables);
// die();
		unset($all_tables);



		$post = $this->input->post();
		$get = $this->input->get();
		if ($post AND !isset($post['update_table'])) 
		{
			// echo '<pre>';
			// print_r($post['del_tables']);
			// echo '<br><br>';
			// echo '<pre>';
			// print_r($post['del_fields']);
			// die();
			// echo '<pre>';
			// print_r($post);
			// die();
			$arr_names = array();
			// echo '<pre>';
			// print_r($this->input->post());
			$text = '<pre>'.print_r($this->input->post(), true);
			foreach ($this->input->post() as $key => $value) {
				$arr_names[] = '$' . $key;
				
			}
			// $text = str_replace('[my_config] =>', '$my_config =', $text);
			// $text = str_replace('[tables] =>', '$tables =', $text);

$data = "<?php
/*********************************************
*
*Путь указывается от корня сайта
*ВНИМАНИЕ!!! Для указания пути AJAX загрузки изображения перейдите в /media/editor/js/plugins/jbimages/config.php
*
*********************************************/
\$my_config = array(
	'filepath' => '{$post['my_config']['filepath']}'
);

\$tables = array(\r\n";
foreach ($post['tables'] as $table_name => $table_config) {
	$data .= "	'{$table_name}' => array(\r\n";
	$data .= "		'title' => '{$table_config['title']}',\r\n";
	$data .= "		'order' => '{$table_config['order']}',\r\n";
	$data .= "		'visible' => '{$table_config['visible']}',\r\n";
	$data .= "		'fields' => array(\r\n";
	foreach($table_config['fields'] as $field_name => $field_config) {
		$data .= "			'{$field_name}' 	=> array(\r\n";
		foreach ($field_config as $key => $value) {
			if ($key=='visible') {
				$key = (($key=='') ? 0 : $key);
			}
			if ($key=='enabled') {
				$key = (($key=='') ? 1 : $key);
			}
			$data .= "				'{$key}' 		=> '{$value}',\r\n";
		}
		$data .= "			),\r\n";
	}
	$data .= "		),\r\n";
	
	if ( ! empty($table_config['relations']) )
	{
		$table_config['relations'] = explode("\n", $table_config['relations']);
		$data .= "		'relations' => array(\r\n";
		foreach($table_config['relations'] as $link) {
			if ( ! empty( $link ) ) {
				$data .= "			'{$link}',\r\n";
			}
		}
		$data .= "		),\r\n";
	}
	$data .= "	),\r\n";
}
$data .= ");";

//header("Content-Type: text/plain");

$file = APPPATH ."config/config_table.php";
$fp = fopen($file, "w+");
fwrite($fp, $data);
fclose($fp);
url::redirect('gen_config');
		}




			//Таблицы, которые вынимаем хранятся в переменной $tables_config
			require_once( APPPATH . 'config/gen_config.php');

			if (empty($tables_config) ) {
				if (url::current(TRUE) != 'gen_config?add_tables') {
				url::redirect('gen_config?add_tables');
				}
				echo 'Добавьте хотя бы 1 таблицу';
			}
			

		//Вывод на редактирование
		if (!$get) {
			$arr_fields_types = array(
				'', 
				'int', 
				'string', 
				'text', 
				'checkbox', 
				'checklist', 
				'select', 
				'date', 
				'file'
			);


			//Выход
			$logout = $this->input->get('logout') ? TRUE : FALSE;
			if ($logout) {
				cookie::delete('id');
			}
			//Проверка авторизованности
			$this->_check_auth();


			$content = NULL;

			$tables = $this->crud_tables;
			$from = 'Получено из config_table';

			if (empty( $tables )) {
				$from = '<font color="red">Файл config_table не найден, поэтому таблицы сгенерированы</font>';
			}

			$array_types = array(
				'int' 		=> array(
					'TINYINT', 'INT', 'SMALLINT', 'MEDIUMINT', 'BIGINT'
				),
				'string' 	=> array(
					'VARCHAR', 'CHAR', 'TINYTEXT'
				),
				'text' 		=> array(
					'TEXT', 'MEDIUMTEXT', 'LONGTEXT'
				),
				'date' 		=> array(
					'DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR'
				)
			);


			$array_table = array();

			$my_config = $this->crud_config;
			if (!empty($my_config)) {
				$array_table['my_config'] = $my_config; 
			} else {
				$array_table['my_config'] = array('filepath' => '');
			}

			//Если уже сгенерировано
			// if ( !empty($tables) AND (!$this->input->get('restart')))
			// {
				//Массив настройки загрузки файлов
				


				foreach ($tables as $key => $value) {
					if (!in_array($key, $tables_config)) continue;
					if (!array_key_exists($key, $arrRealTables)) continue;
					//$array_table['tables'][] = $key;

					//Название таблицы РУС
					$array_table['tables'][$key]['title'] = isset( $value['title'] ) ? $value['title'] : NULL;

					//Тип сортировки таблицы
					$array_table['tables'][$key]['order'] = isset( $value['order'] ) ? $value['order'] : NULL;

					//Связи таблицы
					$array_table['tables'][$key]['relations'] = isset( $value['relations'] ) ? $value['relations'] : array('');

					//Отображение таблицы
					$array_table['tables'][$key]['visible'] = isset( $value['visible'] ) ? $value['visible'] : 1;

					//Поля таблицы
					$array_table['tables'][$key]['fields'] = isset( $value['fields'] ) ? $value['fields'] : NULL;


					//Параметры полей таблицы
					foreach ($array_table['tables'][$key]['fields'] as $name_field => $arr_field) {
						$array_table['tables'][$key]['fields'][$name_field]['type'] = isset( $array_table['tables'][$key]['fields'][$name_field]['type'] ) ? $array_table['tables'][$key]['fields'][$name_field]['type'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['default'] = isset( $array_table['tables'][$key]['fields'][$name_field]['default'] ) ? $array_table['tables'][$key]['fields'][$name_field]['default'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['title'] = isset( $array_table['tables'][$key]['fields'][$name_field]['title'] ) ? $array_table['tables'][$key]['fields'][$name_field]['title'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['extras'] = isset( $array_table['tables'][$key]['fields'][$name_field]['extras'] ) ? $array_table['tables'][$key]['fields'][$name_field]['extras'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['params'] = isset( $array_table['tables'][$key]['fields'][$name_field]['params'] ) ? $array_table['tables'][$key]['fields'][$name_field]['params'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['class'] =  isset( $array_table['tables'][$key]['fields'][$name_field]['class'] ) ? $array_table['tables'][$key]['fields'][$name_field]['class'] : '';

						$array_table['tables'][$key]['fields'][$name_field]['visible'] =  isset( $array_table['tables'][$key]['fields'][$name_field]['visible'] ) ? $array_table['tables'][$key]['fields'][$name_field]['visible'] : 1;

						$array_table['tables'][$key]['fields'][$name_field]['enabled'] =  isset( $array_table['tables'][$key]['fields'][$name_field]['enabled'] ) ? $array_table['tables'][$key]['fields'][$name_field]['enabled'] : 1;
					}
				}
			// }



			//ЕСЛИ НЕТУ config_table
			// if ( empty($tables) OR ($this->input->get('restart')))
			// {
				// echo '<pre>';
				// print_r($tables);
				// die();
				// echo '<pre>';
				// print_r($array_table);



				foreach ($tables_config as $tbl) {
					if (array_key_exists($tbl, $tables)) continue;
					if (!array_key_exists($tbl, $arrRealTables)) continue;

					$array_table['tables'][$tbl] = array();
				}


				foreach ($array_table['tables'] as $table => $arr_table) 
				{
					if (array_key_exists($tbl, $tables)) continue;
					if (!array_key_exists($tbl, $arrRealTables)) continue;

					$array_table['tables'][$table]['title'] = '';
					$array_table['tables'][$table]['order'] = '';
					$array_table['tables'][$table]['visible'] = 1;
					$array_table['tables'][$table]['relations'] = array('');
					$array_table['tables'][$table]['visible'] = 0;

					$tab = array();

						$tab[$table] = $this->db->query("SHOW COLUMNS FROM `$table`")->as_array();

					foreach ($tab as $tabl => $arr) {
						foreach ($arr as $key => $value) {
							$field = $arr[$key]->Field;
							$type = $arr[$key]->Type;
							foreach ($array_types as $glav_type => $types) {
								foreach ($types as $onetype) {
									if (preg_match('~'.$onetype.'~i', $type)) {
										$array_table['tables'][$tabl]['fields'][$field]['type'] = $glav_type;
										continue;
									}
								}
								if ( isset($array_table['tables'][$tabl]['fields'][$field]['type']) ) {
									continue;
								}
							}
							if ( !isset($array_table['tables'][$tabl]['fields'][$field]['type']) ) {
								$array_table['tables'][$tabl]['fields'][$field]['type'] = '';
							}
							$array_table['tables'][$tabl]['fields'][$field]['default'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['title'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['extras'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['params'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['class'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['visible'] = '';
							$array_table['tables'][$tabl]['fields'][$field]['enabled'] = '';
						}

					}
				}
			
			// }

				// echo '<pre>';
				// print_r($array_table);
				// die();

			//filepath
			$filepath = $array_table['my_config']['filepath'];

			$this->template->login = $this->login;
			$this->template->content = View::factory('gen_config/generate_config');
			$this->template->content->from = $from;;
			$this->template->content->content = $array_table;
			$this->template->content->arrRealTables = $arrRealTables;
			$this->template->content->filepath = $filepath;
			$this->template->content->arr_fields_types = $arr_fields_types;
		}


		//Добавляем таблицы
		if ($post AND isset($post['update_table'])) {
			$new_gen_config = "
<?php

\$tables_config = array(
#tables_names#
	);

?>";

			$items = '';
			if (isset($post['tables']) && count($post['tables'])) {
				foreach ($post['tables'] as $key => $tab) {
					$items .= "		'$tab', \r\n";
				}
				$new_gen_config = str_replace('#tables_names#', $items, $new_gen_config);
			}
			$file = APPPATH ."config/gen_config.php";
			$fp = fopen($file, "w+");
			fwrite($fp, $new_gen_config);
			fclose($fp);
			url::redirect('gen_config');
		}

		//Вывод таблиц на добавление
		if ($get) {

			$this->template->login = $this->login;
			$this->template->content = View::factory('gen_config/add_tables');
			$this->template->content->tables = $arrRealTables;
			$this->template->content->tables_config = $tables_config;
		}
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
