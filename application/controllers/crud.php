<?php

/**
CRUD_Controller

@brief Самый главный (после встроенного) контроллер который наследуют остальные контроллеры

*/

class CRUD_Controller extends Template_Controller {

	/** Переменная подключения к БД */
	public $db;

	/** Массив конфигураций config_table */
	public $crud_tables = array();
	public $crud_config = array();

	/** Содержит все таблицы указанные в config_table */
	public $all_table = array();

	/** Максимальный размер файла. Используется только в том случае, если не задано в config_table */
	public $max_file_size = 10240000;

	/** Данные авторизованного пользователя */
	public $user_id, $user_group_id, $login;

	/** Текущий пункт меню */
	public $current_menu = NULL;


	public function __construct()
	{
	/**
		@brief Автоматически выполняется при обращении к классу
	*/
		$this->db = Database::instance();
		if (file_exists( APPPATH ."config/config_table.php") ) {
				require_once APPPATH ."config/config_table.php";
			$this->crud_tables = $tables;
			$this->crud_config = $my_config;
			foreach ($this->crud_tables as $key => $value) {
				if ( isset($this->crud_tables[$key]['visible']) ) {
					if ($this->crud_tables[$key]['visible']==0) {
						continue;
					}
				}
				$this->all_table[] = $key;
			}
		}
		parent::__construct();
	}

	public function _check_auth()
	{
	/**
	@brief - Авторизация
	*/
		$this->user_id = $this->input->cookie('id');
		$this->user_group_id = $this->input->cookie('group_id');
		if($this->user_id && $this->user_group_id && $this->user_group_id==1) {
			//header('Location: http://'. $_SERVER['SERVER_NAME'] . '/panel');
		} else {
			header('Location: http://'. $_SERVER['SERVER_NAME'] . url::base());
			exit();
		}

		$this->login = $this->input->cookie('login');
	}
}