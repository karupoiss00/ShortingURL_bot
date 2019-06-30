<?php
	include('vendor/autoload.php'); //Подключаем библиотеку
	use Telegram\Bot\Api;
	
	const LOGIN =  'o_4dkf0dhc6p';
	const API_KEY =  'R_a9dbe6c319fe4397946c86b8798b7abb';
	const END_POINT =  'http://api.bit.ly/v3';
	const HELP_REPLY = 'Данный бот предназначен для работы со ссылками. Он умеет сокращать или расшифровывать уже сокращённые ссылки. Для того, чтобы воспользоваться ботом, отправьте ему ссылку, которую необходимо сократить.';
	const DB_HOST = 'us-cdbr-iron-east-02.cleardb.net';
	const DB_USER = 'b8ee931ac989b3';
	const DB_PASS = '672ff549';
	const DB_NAME = 'heroku_3fd1c3b404a32b0';
	
	$telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
	$result = $telegram->getWebhookUpdates();
	$text = $result["message"]["text"];
	$chat_id = $result["message"]["chat"]["id"];
	$name = $result["message"]["from"]["username"];
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
        else {
			if (strpos($text, 'http') === FALSE) {
				$text = 'http://'.$text;
			}
			if (strpos($text, 'bit.ly') === FALSE) {
			
				$data = array 
				(
					"chat_id" => $chat_id,
					"first_request" => 'Пусто',
					"second_request" => 'Пусто',
					"third_request" => 'Пусто',
					"fouth_request" => 'Пусто',
					"fifth_request" => $record
				);

				$id = $db->insert ('user_request_history', $data);
				if ($id) {
					$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getShortUrl($text).' Добавлен в БД '.$id]);
				}
				else {
					$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getShortUrl($text)]);
				}
			}
			else {
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getLongUrl($text)]);
			}
        }
    }
	else {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
	}
	
    function getShortUrl($longUrl):string {
        $url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longUrl&login=o_4dkf0dhc6p&apiKey=R_a9dbe6c319fe4397946c86b8798b7abb&format=json&history=1";
        $s = curl_init();
        curl_setopt($s,CURLOPT_URL, $url);
        curl_setopt($s,CURLOPT_HEADER,false);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($s);
        curl_close( $s );
        $obj = json_decode($result, true);
        $res = $obj["results"]["$longUrl"]["shortUrl"];
        if (strlen($res) != 0) {
			
            return $res;
        }
        else {
            return 'Ссылка некорректна';
        }
    }
	
	function getLongUrl($shortUrl):string {
		$query = http_build_query(
			array(
				'login' => LOGIN,
				'apiKey' => API_KEY,
				'shortUrl' => $shortUrl,
				'format' => 'txt'
			)
		);
		$res = file_get_contents(sprintf('%s/%s?%s', END_POINT, 'expand', $query));
		if ($res == 'NOT_FOUND') {
			return "Ссылка не найдена";
		}
		else {
			return $res;
		}
	}
	
	function insertHistoryRecord($record) {
		$db->where ('chat_id', $chat_id);
		
		if ($db->count == 0) {
			$data = array 
			(
				"chat_id" => $chat_id,
				"first_request" => 'Пусто',
				"second_request" => 'Пусто',
				"third_request" => 'Пусто',
				"fouth_request" => 'Пусто',
				"fifth_request" => $record
			);
			$db->insert('user_request_history', $data);	
		}
		else {
			$users_history = $db->get('user_request_history');
			$history = $db->getOne('user_request_history', array('first_request', 'second_request', 'third_request', 'fouth_request', 'fifth_request'));
			
			$data = array 
			(
				"chat_id" => $chat_id,
				"first_request" => $history[1],
				"second_request" => $history[2],
				"third_request" => $history[3],
				"fouth_request" => $history[4],
				"fifth_request" => $record
			);
			$db->update('users', $data);
		}
		
	}
