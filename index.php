<?php
	require_once('vendor/autoload.php');
	require_once('url_handler.php');
	require_once('db_handler.php');
	require_once('config.php');
	
	use Telegram\Bot\Api;

	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["first_name"];
    $db = initDB();

	if($text) {
		if ($text == '/start') {
			if (strlen($name)) {
				$reply = 'Добро пожаловать, '.$name.'!';		
			}
			else {
				$reply = 'Добро пожаловать, Незнакомец!';
			}
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
		}
		elseif ($text == '/help') {
			$reply = HELP_REPLY;
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		}
		elseif ($text == '/history') {
			$reply = getUserHistory($db, $chat_id);
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		}
		else {
			$reply = urlHandle($db, $chat_id, $text);
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);	
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}