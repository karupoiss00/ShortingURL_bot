<?php
	require_once('vendor/autoload.php');
	require_once('url_handler.php');
	require_once('db_handler.php');
	require_once('config.php');
	require_once('functions.php');
	
	use Telegram\Bot\Api;

	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["first_name"];
    $db = initDB();

	if($text) {
		if ($text == START_COMMAND) {
			$reply = parseGreeting($name);
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
		}
		elseif ($text == HELP_COMMAND) {
			$reply = HELP_REPLY;
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		}
		elseif ($text == HISTORY_COMMAND) {
			$reply = getUserHistory($db, $chat_id);
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		}
		else {
			$reply = urlHandle($db, $chat_id, $text);
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);	
		}
	}
	else {
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => SEND_ME_TEXT ]);
	}