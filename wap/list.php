<?php
$origin_classes = get_declared_classes();

require 'config.php';
function __autoload($a) {
	$class = 'class ' . $a . '{}';
	eval($class);
}


require BASEPATH . '/' . $_GET['file_name'];

$new_classes = get_declared_classes();

$classes_diff = array_diff($new_classes, $origin_classes);

foreach ($classes_diff as $v) {
// 	if ($v == 'CI_Controller') continue;
	$method_list = get_class_methods($v);
	if ($method_list) {
		$useful_classes[$v] = $method_list;
	}
}

foreach ($useful_classes as $class_name => $method_list) {
	foreach ($method_list as $method) {
		$c_m = new ReflectionMethod($class_name, $method);
		$temp = $c_m->getDocComment();
		// 	pr($c_m->getParameters());
		$t = explode("\r\n", $temp);
		$tt = '';
		if (isset($t[1])) $tt = explode('/', $t[1]);
		$interface[$method] = isset($tt[1]) ? trim($tt[1]) : '';
	}
}

function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<body>
		<table>
			<tbody>
<?php foreach ($interface as $k => $v) {?>
				<tr><td><a href="<?php echo 'detail.php?file_name=' . $_GET['file_name'] . '&class=' . $class_name . '&method=' . $k;?>" target="_blank"><?php echo $v;?></a></td></tr>
<?php }?>
			</tbody>
		</table>	
	</body>
</html>
