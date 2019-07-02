<?php
	include('vendor/autoload.php');
	
	const DB_HOST = 'us-cdbr-iron-east-02.cleardb.net';
	const DB_USER = 'b8ee931ac989b3';
	const DB_PASS = '672ff549';
	const DB_NAME = 'heroku_3fd1c3b404a32b0';
	const CHAT_ID = 'chat_id';
	const FIRST_REQUEST = 'first_request';
	const SECOND_REQUEST = 'second_request';
	const THIRD_REQUEST = 'third_request';
	const FOURTH_REQUEST = 'fourth_request';
	const FIFTH_REQUEST = 'fifth_request';
	const EMPTY_SLOT = '<пусто>';
	
	$db = new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$db->autoReconnect = true;
	
	function getUserHistory($userId): string {
		$res = getUserRow($userId);
		
		if (count($res)) {
			return parseDataToHistory($res);
		}
		else {
			$data = [
				CHAT_ID => $chat_id,
				FIRST_REQUEST => EMPTY_SLOT,
				SECOND_REQUEST => EMPTY_SLOT,
				THIRD_REQUEST => EMPTY_SLOT,
				FOURTH_REQUEST => EMPTY_SLOT,
				FIFTH_REQUEST => EMPTY_SLOT				
			];
			insertUserRow($data);
			return parseDataToHistory($res);
		}
	}
	
	function updateUserHistory($userId, $lastAction) {
		$db->where(CHAT_ID, $userId);
		$record = $db->getOne('user_request_history');
		if (count($record)) {
			$record[FIRST_REQUEST] = $record[SECOND_REQUEST];
			$record[SECOND_REQUEST] = $record[THIRD_REQUEST];
			$record[THIRD_REQUEST] = $record[FOURTH_REQUEST];
			$record[FOURTH_REQUEST] = $record[FIFTH_REQUEST];
			$record[FIFTH_REQUEST] = $lastAction;
			updateUserRow($record);
		}
		else {
			$record = [
				CHAT_ID => $chat_id,
				FIRST_REQUEST => EMPTY_SLOT,
				SECOND_REQUEST => EMPTY_SLOT,
				THIRD_REQUEST => EMPTY_SLOT,
				FOURTH_REQUEST => EMPTY_SLOT,
				FIFTH_REQUEST => $lastAction
			];
			insertUserRow($record);
		}
	}
	
	function getUserRow($userId): array {
		$db->where(CHAT_ID, $chat_id);
		return $db->getOne('user_request_history');
	}
	
	function insertUserRow($data) {
		$db->insert('user_request_history', $data);
	}
	
	function updateUserRow($data) {
		$db->update('user_request_history', $data);
	}
	
	function parseDataToHistory($data): string {
		$history = "Последние действия:\n ";
		$data = array_slice($data , 1);
		foreach ($data as $record) {
			$history .= $record."\n";
		}
		
		return $history;
	}