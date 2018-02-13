<?php
// namespace php_api;
class Mapi {
	private $project_list;
	public $selected_project;
	public $selected_file;
	public $customer_file_path;
	
	public function __construct() {
		if (isset($_GET['p'])) $this->selected_project = $_GET['p'];
		if (isset($_GET['f'])) $this->selected_file = $_GET['f'];
		$this->customer_file_path = 'customer';
	}
	
	public function __get($var) {
		if (!isset($this->$var)) {
			self::$var();
			return $this->$var;
		}
	}
	
	public function __set($k, $v) {
		$this->$k = $v;
	}
	
	private function project_list() {
// 		$this->project_list = array();
		$this->project_list = scandir($this->customer_file_path);
		unset($this->project_list[0], $this->project_list[1]);
	}

	private function file_list() {
		$this->file_list = array();
		if ($this->selected_project) {
			$config_json = file_get_contents($this->customer_file_path . '/' . $this->selected_project . '/config.json');
			$config = json_decode($config_json, true);
			foreach ($config as $k => $v) {
				define($k, $v);
			}
			$this->file_list = scandir(BASEPATH);
			unset($this->file_list[0], $this->file_list[1]);
		}
	}
	
	private function api_list() {
		$this->api_list = array();
		$this->class_name = '';
		if ($this->selected_file) {
			$origin_classes = get_declared_classes();
			function __autoload($a) {
				$class = 'class ' . $a . '{}';
				eval($class);
			}
			include BASEPATH . '/' . $this->selected_file;
			$new_classes = get_declared_classes();
			$classes_diff = array_diff($new_classes, $origin_classes);
			foreach ($classes_diff as $v) {
				// 		if ($v == 'CI_Controller') continue;
				$method_list = get_class_methods($v);
				if ($method_list) {
					$useful_classes[$v] = $method_list;
				}
			}
		
			if (isset($useful_classes) && $useful_classes) {
				foreach ($useful_classes as $class_name => $method_list) {
					foreach ($method_list as $method) {
						$c_m = new ReflectionMethod($class_name, $method);
						$temp = $c_m->getDocComment();
						$temp = str_replace(array("\r\n", "\r", "\n"), '|R|', $temp);
						$t = explode("|R|", $temp);
						$t = str_replace("\t", ' ', $t);
						if (isset($t[1])) {
							$tt = explode(' ', $t[1]);
							foreach ($tt as $k => $v) {
								if ($v == ' ') unset($tt[$k]);
							}
							$interface[$method] = end($tt);
						}
					}
				}
			}
			$this->api_list = $interface;
			$this->class_name = $class_name;
		}
	}
	
	public function group_list() {
		
	}
	
	public function detail() {
		
	}
	
	public function process() {
		
	}
}
?>