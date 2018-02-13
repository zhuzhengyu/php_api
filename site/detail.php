<?php
require 'config.php';
function __autoload($a) {
	$class = 'class ' . $a . '{}';
	eval($class);
}
require BASEPATH . '/' . $_GET['file_name'];
$file_name = $_GET['file_name'];
$class = $_GET['class'];
$method = $_GET['method'];
$c_m = new ReflectionMethod($class, $method);

$temp = $c_m->getDocComment();
$a = explode("\r\n", $temp);
// pr($a);
foreach ($a as $k => $v) {
	if (!stristr($v, '@apiParam')) continue;
	$v = str_replace(array("\t", ']', '[', '*', '@apiParam'), array(' ', ' ', '', '', ''), $v);
	$row = explode(' ', $v);
	$new_row = array();
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

function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<script src="../js/jquery-3.2.1.js"></script>
<script>
	var auto_fill = true;//@TODO,自动填充
	
	function set_data() {
		
	}
	
	function get_data() {
		var url = 'http://magic/magic_api/tool_bar/get_data.php';
		$.get(url, {}, function(response) {
			$('#right_main').html(response);
			if (auto_fill == true) auto();
			});
	}

	function auto() {
		$(".js_right_data").each(function(){
			var value = $(this).val();
			var key = $(this).attr('name');
			$('#left').find('.' + key).val(value);
			 });
	}

	function modify_param(key, value) {
		var url = 'http://magic/magic_api/tool_bar/set_data.php';
		var data = key + '|' + value;
		$.get(url, {'data':data}, function(response) {
			get_data();
		});
	}
</script>
		<style>
		#main{width:960px;height:auto;}
		#left{width:70%;height:600px;}
		#right{width:20%;height:600px;margin-left:10px;}
		#left,#right{float:left;}
		</style>
	</head>
	<body>
		<div id="main">
			<div id="left">
				<div><input type="button" value="自动填充请求参数" onClick="auto();"/></div>
				<form action="process.php?method=<?php echo $method;?>&class=<?php echo $class;?>&file_name=<?php echo $file_name;?>" method="post" target="_blank">
					<table>
						<tbody>
							<tr>
								<th>参数:</th>
								<th>填写参数值:</th>
								<th>描述</th>
							</tr>
<?php foreach($save as $k => $v) {?>
							<tr>
								<td><?php echo $v['param'];?></td>
								<td><input type="text" class="<?php echo $v['param'];?>" name="<?php echo $v['param'];?>" value=""/></td>
								<td><?php echo $v['type'] . $v['desc']?></td>
							</tr>
<?php }?>
						</tbody>
					</table>
					<br/><br/>
					<input type="submit" value="提交"/>
				</form>
			</div>
			
			<div id="right">
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
				<div id="right_main"></div>
				<div id="right_bottom"></div>
			</div>
		</div>
	</body>
</html>

<script>
get_data();
</script>