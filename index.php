<?php
	include('vendor/autoload.php');
	include('url_handler.php');
	include('db_handler.php');
	use Telegram\Bot\Api;
	
	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["first_name"];
	
	
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
			$row = $db->getOne('user_request_history');
			if (count($row)) {
				$history = array_slice($row , 1);
				$reply = "Последние действия:\n ";
				foreach ($history as $record) {
					$reply .= $record."\n";
				}
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
			}
			else {
				$data = [
					CHAT_ID => $chat_id,
					FIRST_REQUEST => '<пусто>',
					SECOND_REQUEST => '<пусто>',
					THIRD_REQUEST => '<пусто>',
					FOURTH_REQUEST => '<пусто>',
					FIFTH_REQUEST => '<пусто>'
				];
				$db->insert('user_request_history', $data);
				$reply = "Последние действия:\n";
				$history = array_slice($data , 1);
				foreach ($history as $record) {
					$reply .= $record."\n";
				}
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
			}
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
				$db->where('chat_id', $chat_id);
				$record = $db->getOne('user_request_history');
				if (count($record)) {
					$record[FIRST_REQUEST] = $record[SECOND_REQUEST];
					$record[SECOND_REQUEST] = $record[THIRD_REQUEST];
					$record[THIRD_REQUEST] = $record[FOURTH_REQUEST];
					$record[FOURTH_REQUEST] = $record[FIFTH_REQUEST];
					$record[FIFTH_REQUEST] = $reply;
					$db->update('user_request_history', $record);
				}
				else {
					$data = [
						CHAT_ID => $chat_id,
						FIRST_REQUEST => '<пусто>',
						SECOND_REQUEST => '<пусто>',
						THIRD_REQUEST => '<пусто>',
						FOURTH_REQUEST => '<пусто>',
						FIFTH_REQUEST => $reply
					];
					$db->insert('user_request_history', $data);
				}
			}			
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}