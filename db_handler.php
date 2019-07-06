<?php
	require_once('vendor/autoload.php');
	require_once('config.php');
	
	function initDB(): MysqliDb {
		$db = new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$db->autoReconnect = true;
		return $db;
	}

	function updateHistory(MysqliDb $db, $lastAction, $userId) {
		$record = getRow($db, $userId);
		if (count($record)) {
			$record[FIRST_REQUEST] = $record[SECOND_REQUEST];
			$record[SECOND_REQUEST] = $record[THIRD_REQUEST];
			$record[THIRD_REQUEST] = $record[FOURTH_REQUEST];
			$record[FOURTH_REQUEST] = $record[FIFTH_REQUEST];
			$record[FIFTH_REQUEST] = $lastAction;
			updateRow($db, $record);
		}
		else {
			$data = [
				CHAT_ID => $userId,
				FIRST_REQUEST => EMPTY_SLOT,
				SECOND_REQUEST => EMPTY_SLOT,
				THIRD_REQUEST => EMPTY_SLOT,
				FOURTH_REQUEST => EMPTY_SLOT,
				FIFTH_REQUEST => $lastAction
			];
			insertRow($db, $data);
		}
	}
	
	function getUserHistory(MysqliDb $db, $userId): string {
		$row = getRow($db, $userId);
		if (count($row)) {
			return formatRowToStr($row);
		}
		else {
			$data = [
				CHAT_ID => $userId,
				FIRST_REQUEST => EMPTY_SLOT,
				SECOND_REQUEST => EMPTY_SLOT,
				THIRD_REQUEST => EMPTY_SLOT,
				FOURTH_REQUEST => EMPTY_SLOT,
				FIFTH_REQUEST => EMPTY_SLOT
			];
			insertRow($db, $data);
			return formatRowToStr($data);
		}
	}
	
	function formatRowToStr($data): string {
		$data = array_slice($data , 1);
		$res = "Последние действия:\n";
		foreach ($data as $record) {
			$res .= $record."\n";
		}
		return $res;
	}
	
	function getRow(MysqliDb $db, $userId) {
		$db->where(CHAT_ID, $userId);
		$row = $db->getOne('user_request_history');
		return $row;
	}
	
	function insertRow(MysqliDb $db, $row) {
		$db->insert('user_request_history', $row);
	}
	
	function updateRow(MysqliDb $db, $row) {
		$db->update('user_request_history', $row);
	}