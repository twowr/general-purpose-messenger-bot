<?php

include "../secret.php";

error_reporting(0);

function send_to($reciever_id, $message) {
    include "../secret.php";
    
	$res = [
		"recipient" => [
			"id" => $reciever_id,
		],
		"message" => [
			"text" => $message,
		],
	];
	$ch = curl_init("https://graph.facebook.com/v16.0/me/messages?access_token=".$access_token);		
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($res));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_exec($ch);
    curl_close($ch);
}

function handle_message($sender_id, $message) {
    include "commands.php";
    
	$expression = explode(" ", $message);
	$res = "";
    
	if (array_key_exists($expression[0], $commands)) {
		$command = $expression[0];
		$args = $expression;
		unset($args[0]);
		$args = array_values($args);
		
		print_r($args);
		
		$res = $commands[$command]($args);
	    send_to($sender_id, $res);
	}

	return $res;

}

$req["method"] = $_SERVER["REQUEST_METHOD"];
switch ($req["method"]) {
	case "GET":
	    include "../secret.php";
		$mode = $_REQUEST["hub_mode"];
		$token = $_REQUEST["hub_verify_token"];
		$challenge = $_REQUEST["hub_challenge"];
		if ($mode && $token) {
			if ($mode === "subscribe" && $token === $verify_token ) {
				$res["status_code_header"] = "HTTP/1.1 200 OK";
			    header($res["status_code_header"]);
				echo $challenge;
			}
		} else {
			echo "you doesn't have access to this page";
		}
		break;		
	case "POST":
		$body = json_decode(file_get_contents("php://input"), true);
		if ($body["field"] === "messages") {
		    $sender_id = $body["value"]["sender"]["id"];
    		$message = $body["value"]["message"]["text"];
    		$res = handle_message($sender_id, $message);
			file_put_contents("../log_file.log", "\n".file_get_contents("php://input")."\nrespond: ".$res, FILE_APPEND | FILE_USE_INCLUDE_PATH);
    		$status["status_code_header"] = "HTTP/1.1 200 OK";
			header($status["status_code_header"]);
		}
}

?>
