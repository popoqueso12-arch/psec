<?php
require('../panel/include/setings.php');

if (empty($_COOKIE['id'])) {
	return;
}
$reg = (string) $_COOKIE['id'];
if ($reg === '' || !ctype_digit($reg)) {
	return;
}
$es = status($reg);
echo ($es !== null && $es !== '') ? (int) $es : '';