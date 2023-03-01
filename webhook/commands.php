<?php

$commands = [
	".test" => function ($args) {
		$res_msg = implode("",$args);
		return $res_msg;
	}
];

?>
