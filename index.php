<?php
	include('vendor/autoload.php'); //Подключаем библиотеку
	include('url_handler.php');
	use Telegram\Bot\Api;
	
	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["first_name"];
	$db = new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$db->autoReconnect = true;
	
	
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
			$db->where('chat_id', $chat_id);
			$res = $db->getOne('user_request_history');
			if (count($res)) {
				$history = array_slice($res , 1);
				$reply = 'Последние действия:
				';
				foreach ($history as $record) {
					$reply .= $record.'
					';
				}
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
			}
			else {
				$data = [
					'chat_id' => $chat_id,
					'first_request' => ' <пусто>',
					'second_request' => '<пусто>',
					'third_request' => '<пусто>',
					'fourth_request' => '<пусто>',
					'fifth_request' => '<пусто>'
				];
				$db->insert('user_request_history', $data);
				$reply = 'Последние действия:
				';
				$history = array_slice($data , 1);
				foreach ($history as $record) {
					$reply .= $record.'
					';
				}
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
			}
		}
		else {
			if (strpos($text, 'http') === FALSE) {
				$text = 'http://'.$text;
			}
			if (strpos($text, 'bit.ly') === FALSE) {
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getShortUrl($text)]);
			}
			else {
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getLongUrl($text)]);
			}
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}