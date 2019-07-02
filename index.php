<?php
	include('vendor/autoload.php');
	include('url_handler.php');
	include('globals.php');
	include('db_handler.php.');
	
	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["first_name"];

	use Telegram\Bot\Api;
	
	
	if($text) {
		if ($text == '/start') {
			if (strlen($name) == 0) {
				$reply = 'Добро пожаловать, Незнакомец!';
			}
			else {
				$reply = 'Добро пожаловать, '.$name.'!';
			}
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
		}
		elseif ($text == '/help') {
			$reply = HELP_REPLY;
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		}
		elseif ($text == '/history') {
			$reply = getUserHistory($chat_id);
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
			if ($reply != 'Ссылка некорректна') {
				updateUserHistory($reply);
			}			
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}