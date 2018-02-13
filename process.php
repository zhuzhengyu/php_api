<?php
require 'config.php';
require 'Mapi.class.php';

$title = '@TODO';//@TODO

if (isset($_GET['p']) && $_GET['p']) {
	$project = $_GET['p'];
	include 'project/' . $_GET['p'] . '/config.php';
	$file_list = scandir(BASEPATH);
	unset($file_list[0], $file_list[1]);
}

if (isset($_GET['f']) && $_GET['f']) {
	$origin_classes = get_declared_classes();
	function __autoload($a) {
		$class = 'class ' . $a . '{}';
		eval($class);
	}
	$file = $_GET['f'];
	include BASEPATH . '/' . $file;
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
				// 				$temp = str_replace("\r", '|R|', $temp);
				// 				$temp = str_replace("\n", '|R|', $temp);
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
}

if (isset($_GET['c']) && isset($_GET['m'])) {
	$class = $_GET['c'];
	$method = $_GET['m'];
	$c_m = new ReflectionMethod($class, $method);

	$temp = $c_m->getDocComment();
	$temp = str_replace(array("\r\n", "\r", "\n"), '|R|', $temp);
	$a = explode("|R|", $temp);
	// 	pr($a);
	foreach ($a as $k => $v) {
		if (!stristr($v, '@apiParam')) continue;
		$v = str_replace(array("\t", ']', '[', '*', '@apiParam'), array(' ', ' ', '', '', ''), $v);
		$row = explode(' ', $v);
		$new_row = array();
		// 		pr($v);
		foreach($row as $kk => $vv) {
			if ($vv != '') {
				if (isset($new_row[2])) {
					$new_row[2] .= $vv;
				} else {
					$new_row[] = $vv;
				}
			}
		}
		$save[$k]['type']	= $new_row[0];
		$save[$k]['param']	= $new_row[1];
		$save[$k]['desc']	= $new_row[2];
	}
}

$url = URL . '/index.php';//@TODO
$list = scandir('project');
unset($list[0], $list[1]);

function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>