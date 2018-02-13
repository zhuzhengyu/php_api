<?php
$data = $_GET['data'];

$data_arr = explode('|', $data);
$key = $data_arr[0];
$value = $data_arr[1];
setcookie($key, $value);

pr($_COOKIE);

function pr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}



