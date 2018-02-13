<?php
require 'config.php';
require 'function.php';
$data['BASEPATH'] = 'D:\work\workspace\service_apigateway\application\controllers\app\v2';
$data['TARGET_URL'] = 'http://stagingnirvana3.fruitday.com';

if (isset($_GET['p'])) {
	$file_path = 'customer/' . $_GET['p'];
	$file = $file_path . '/config.json';
	if (!file_exists($file_path)) mkdir($file_path);
	$contents = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	file_put_contents($file, $contents);
}
// if (isset($_GET['p'])) {
// 	$file = CUSTOMER_CONFIG . '/' . $_GET['p'] . '.json';
// 	$contents = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
// // 	$contents = json_encode($data);
// 	pr($file);
// 	pr($contents);
// 	file_put_contents($file, $contents);
// }

php_api\pr($data);







?>


