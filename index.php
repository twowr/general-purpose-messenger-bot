<?php

$req = $_SERVER["REQUEST_METHOD"];

include "secret.php";

switch ($req) {
    case "GET":
		if ($_REQUEST["token"] === $verify_token) {
			$res["status_code_header"] = "HTTP/1.1 200 OK";
        	$res["body"] = '
            	<html lang="en">
            		<head>
                		<meta charset="UTF-8">
                		<meta http-equiv="X-UA-Compatible" content="IE=edge">
                		<meta name="viewport" content="width=device-width, initial-scale=1.0">
                		<title>owo</title>
            		</head>
            		<body>
					'.file_get_contents("log_file.log").' 
            		</body>
            	</html>
        	';
			header($res["status_code_header"]);
			echo $res["body"];
		};
        break;
}

?>
