<?php
require 'config.php';
try {
	if (!is_dir(BASEPATH)) throw new \Exception(BASEPATH . ':该目录不存在,请检查目录');
	
	$list = scandir(BASEPATH);
	unset($list[0], $list[1]);

// 	$html  = '<table><tbody>';
// 	foreach ($list as $v) {
// 		$html .= '<tr><td><a href="list.php?file_name=' . $v . '">' . $v . '</a></td></tr>';
// 	}
// 	$html .= '</tbody></table>';
// 	echo $html;
} catch (Exception $e) {
	pr($e->getMessage());
}
function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
?>

<table>
<?php
foreach ($list as $k => $v) {
?>
	<tr>
		<td><a href="<?php echo URL;?>/site/list.php?file_name=<?php echo $v;?>"><?php echo $v;?></a></td>
	</tr>
<?php
}
?>
</table>