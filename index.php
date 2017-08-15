<?php
require 'config.php';
require 'Mapi.class.php';
require 'restclient.php';


//@TODO,读取配置文件










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
	$first_line_arr = explode(' ', str_replace(array('{', '}'), '', $a[1]));
	$method = $first_line_arr[3];
	foreach ($a as $k => $v) {
		if (stristr($v, '@apiSampleRequest')) {
			$request_uri = explode(' ', $v);
			$request_uri = end($request_uri);
		}
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

$this_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$request_url = TARGET_URL . '/' . trim($request_uri, '/');
	$rc = new RestClient();
	$result = $rc->execute($request_url, $method, http_build_query($_POST));
// 	$response = $result->response;
// 	$response = json_encode(json_decode($result->response, true));
	$response = json_encode(json_decode($result->response, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
// pr($request_url);
// pr($method);
// pr($result);
// pr($response);
$url = URL . '/index.php';//@TODO
$list = scandir('project');
unset($list[0], $list[1]);

function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $title;?></title>
<script>
var site_url = '<?php echo URL;?>';
var css_site = '<?php echo CSS_SITE;?>';
// function group_list() {
// 	var url = site_url + '/site/group_list.php';
// 	$.get(url, {}, function(response) {
// 		$('#left').html(response);
// 	});
// }
</script>
<script src="<?php echo CSS_SITE;?>/js/jquery-3.2.1.js"></script>
<link rel="stylesheet" href="<?php echo CSS_SITE;?>/css/base.css" media="all" />
</head>
<body>
	<div id="main">
		<div id="left">
---------------项目---------------
			<div id="left_top">
				<table>
<?php
foreach ($list as $k => $v) {
?>
					<tr>
						<td><a href="<?php echo $url . '?p=' . $v;?>"><?php echo $v;?></a></td>
					</tr>
<?php
}
?>			
				</table>
			</div>
---------------类文件---------------
			<div id="left_main">
				<table>
<?php
if (isset($file_list) && is_array($file_list)) {
	foreach ($file_list as $k => $v) {
?>
					<tr>
						<td><a href="<?php echo $url . '?p=' . $project . '&f=' . $v;?>"><?php echo $v;?></a></td>
					</tr>
<?php
	}
}
?>
				</table>
			</div>
---------------方法---------------
			<div id="left_bottom">
				<table>
<?php
if (isset($interface) && is_array($interface)) {
	foreach ($interface as $k => $v) {
?>
					<tr>
						<td><a href="<?php echo $url . '?p=' . $project . '&f=' . $file. '&c=' . $class_name . '&m=' . $k;?>"><?php echo $v;?></a></td>
					</tr>
<?php
	}
}
?>
				</table>
			</div>
		</div>
		<div id="middle">
			<div id="middle_top">
			</div>
----------接口详情----------
			<div id="middle_main">
				<form action="<?php echo $this_url;?>" method="post">
				<table>
					<tr>
						<th>参数:</th>
						<th>填写参数值:</th>
						<th>参数类型</th>
						<th>描述</th>
					</tr>
<?php
if (isset($save)) {
	foreach($save as $k => $v) {?>
					<tr>
						<td><?php echo $v['param'];?></td>
						<td><input type="text" class="<?php echo $v['param'];?>" name="<?php echo $v['param'];?>" value="<?php if (isset($_POST[$v['param']])) echo $_POST[$v['param']];?>"/></td>
						<td><?php echo $v['type'];?></td>
						<td><?php echo $v['desc'];?></td>
					</tr>
<?php
	}
}
?>
				</table>
				<br/>
				<input type="submit" value="commit"/>
				</form>
			</div>
			<div id="middle_bottom"><?php isset($response) && pr($response);?></div>
		</div>
		<div id="right">
----------新增参数----------
			<div id="right_top">
				<table>
					<tr>
						<th>参数</th>
						<th>参数值</th>
						<th>操作</th>
					</tr>
					<tr>
						<td><input type="text" id="js_set_key" value=""/></td>
						<td><input type="text" id="js_set_value" value=""/></td>
						<td><input type="button" value="提交变更" onClick="modify_param($('#js_set_key').val(), $('#js_set_value').val());"/></td>
					</tr>
				</table>
			</div>
----------自动填充参数----------
			<div id="right_main"></div>
			<div id="right_bottom"></div>
		</div>
	</div>
</body>
</html>

<script>
	var auto_fill = true;//@TODO,自动填充
	
	function set_data() {
		
	}
	
	function get_data() {
		var url = site_url + '/tool_bar/get_data.php';
		$.get(url, {}, function(response) {
			$('#right_main').html(response);
			if (auto_fill == true) auto();
			});
	}

	function auto() {
		$(".js_right_data").each(function(){
			var value = $(this).val();
			var key = $(this).attr('name');
			$('#middle').find('.' + key).val(value);
			 });
	}

	function modify_param(key, value) {
		var url = site_url +　'/tool_bar/set_data.php';
		var data = key + '|' + value;
		$.get(url, {'data':data}, function(response) {
			get_data();
		});
	}

	get_data();
</script>

<script>

var jsonObject= $.parseJSON('<?php echo $response;?>');
// var formatJsonStr=JSON.stringify(jsonObject,undefined, 2);
// $('#middle_bottom').html('<pre>' + formatJsonStr + '</pre>');
//group_list();
</script>