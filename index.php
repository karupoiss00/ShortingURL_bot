<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api;
	
	define('LOGIN', 'o_4dkf0dhc6p');
	define('API_KEY', 'R_a9dbe6c319fe4397946c86b8798b7abb');
	define('END_POINT', 'http://api.bit.ly/v3');
	
    $telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY');
    $result = $telegram->getWebhookUpdates();
    $text = $result["message"]["text"];
    $chat_id = $result["message"]["chat"]["id"];
    $name = $result["message"]["from"]["username"];

    if($text){
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
            $reply = 'Данный бот предназначен для работы со ссылками. Он умеет сокращать или расшифровывать уже сокращённые ссылки. Для того, чтобы воспользоваться ботом, отправьте ему ссылку, которую необходимо сократить.';
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
        else {
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => stringHandler($text)]);
        }
    }
    else {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Отправьте текстовое сообщение.' ]);
    }
	
	function stringHandler($string) {
		if(stristr($string, 'http://bit.ly/') === FALSE)) {
			return createShortLink($string);
		}
		else {
			return getLongUrl($string);
		}
	}
	/*
    function getSmallLink($longurl){
        $url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=o_4dkf0dhc6p&apiKey=R_a9dbe6c319fe4397946c86b8798b7abb&format=json&history=1";
        $s = curl_init();
        curl_setopt($s,CURLOPT_URL, $url);
        curl_setopt($s,CURLOPT_HEADER,false);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($s);
        curl_close( $s );
        $obj = json_decode($result, true);
        $res = $obj["results"]["$longurl"]["shortUrl"];
        if (strlen($res) != 0) {
            return 'Ссылка сокращена - '.$res;
        }
        elseif {
            return 'Ссылка некорректна';
        }
    }
	*/
	
	function createShortLink($long_url)
	{
		$long_url = urlencode($long_url);
		$bitly_response = json_decode(file_get_contents('http://api.bit.ly/v3/shorten?login='.LOGIN.'&apiKey='.API_KEY.'&longUrl='.$long_url.'&format=json'));
		$short_url = $bitly_response->data->url;
		return $short_url;
	}
	function getLongUrl($shortUrl)
	{
		$query = http_build_query(
			array(
				'login' => LOGIN,
				'apiKey' => API_KEY,
				'shortUrl' => $shortUrl,
				'format' => 'txt'
			)
		);
		return file_get_contents(sprintf('%s/%s?%s', END_POINT, 'expand', $query));
	}
?>