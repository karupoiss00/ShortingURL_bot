<?php
	require_once('vendor/autoload.php');
	require_once('url_handler.php');
	require_once('db_handler.php');
	
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
			if (strpos($text, 'http') === FALSE) {
				$text = 'http://'.$text;
			}

			if (strpos($text, 'bit.ly') === FALSE) {
				$reply = getShortUrl($text);
			}
			else {
				$reply = getLongUrl($text);
			}
			
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);

			if ($reply != ERROR_MESS) {

				updateHistory($db, $reply, $chat_id);
			}
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}