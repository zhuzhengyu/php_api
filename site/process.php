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

unset($_GET['file_name'], $_GET['class'], $_GET['method']);
$c_m = new ReflectionMethod($class, $method);

$temp = $c_m->getDocComment();
$a = explode("\r\n", $temp);
foreach ($a as $k => $v) {
	if (!stristr($v, '@apiSampleRequest')) continue;
	$v = str_replace(array("\t"), array(' '), $v);
	$row = explode(' ', $v);
	$url = end($row);
}
// $api_url = URL . $url . '?' . http_build_query($_GET);
$api_url = URL . $url . '?' . http_build_query($_POST);
$post_data = $_POST;//@TODO
pr($api_url);
function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>

<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<body>
		<form action="<?php echo $api_url;?>" method="post" id="myForm">
			<table>
				<tbody>
<?php foreach ($post_data as $k => $v) {?>
					<tr>
						<td>
							<?php echo $v. ' ';?>
						</td>
						<td>
							<input type="text" name="<?php echo $k;?>" value="<?php echo $v;?>"/>
						</td>
					</tr>
<?php }?>
				</tbody>
			</table>
			<input type="test" name="sign" value="<?php //echo $pro_validate_sign;?>"/>
		</form>
	</body>
	<script type="text/javascript">
	  document.getElementById("myForm").submit();
	</script>
</html>


