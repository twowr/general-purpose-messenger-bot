<?php

$access_token;
$verify_token;

include "../secret.php";

function send_to($reciever_id, $message) {
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
		
		$res = $commands[$command]($args);
	}

	send_to($sender_id, $res);
}

$req["method"] = $_SERVER["REQUEST_METHOD"];
switch ($req["method"]) {
	case "GET":
		$mode = $_REQUEST["hub_mode"];
		$token = $_REQUEST["hub_verify_token"];
		$challenge = $_REQUEST["hub_challenge"];
		if ($mode && $token) {
			if ($mode === "subscribe" && $token === $verify_token ) {
				http_respond_code(200);
				echo $challenge;
			}
		} else {
			echo "you doesn't have access to this page";
		}
		break;		
	case "POST":
		$body = json_decode(file_get_contents("php://input"), true);
		error_log("\n".$body."\n", 3, "./log_file.log");
		if ($body["field"] === "messages") {
		    $sender_id = $body["value"]["sender"]["id"];
    		$message = $body["value"]["message"]["text"];
    		handle_message($sender_id, $message);
    		http_respond_code(200);
		}
}

?>
