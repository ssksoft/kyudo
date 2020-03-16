<?php
function connect($params){
	ini_set( 'display_errors', 1 );
	ini_set( 'error_reporting', E_ALL );
	// check parameter
	
	if(! isset($params['host'])) $params['host'] = '';
	if(! isset($params['port'])) $params['port'] = '';
	if(! isset($params['database'])) $params['database'] = '';
	if(! isset($params['user'])) $params['user'] = '';
	if(! isset($params['password'])) $params['password'] = '';
	
	// connetc with postgresql DB
	$conStr = sprintf("pgsql: dbname=%s host=%s port=%d",
		$params['database'],	
		$params['host'],
		$params['port']
	);
	echo("new PDO\n");

	$pdo = new PDO($conStr, $params['user'],$params['password']);
	$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	$pdo->query("SET client_encoding TO 'UTF8'");

	return $pdo;
}

function insertKyudo($pdo, $datetime, $player_name='', $hit_record=''){
	ini_set( 'display_errors', 1 );
	ini_set( 'error_reporting', E_ALL );
	// Prepare INSERT statement
	$sql = 'INSERT INTO kyudo_tbl'
	.'(datetime, player_name, hit_record)'
	.' VALUES'
	.' (:datetime, :player_name, :hit_record)';
	$stmt = $pdo->prepare($sql);

	// Pass value to statement
	$stmt->bindValue(':datetime', strftime("%F %T", strtotime($datetime)));
	$stmt->bindValue(':player_name', pg_escape_string($player_name));
	$stmt->bindValue(':hit_record', pg_escape_string($hit_record));
	
	// Execute statement
	$stmt->execute();

	// Return numbered ID
	return $pdo->lastInsertId('id');
}

function updateKyudo($pdo, $id, $datetime, $subject, $detail){
	//Prepare UPDATE statement
	$sql = 'UPDATE todo_tbl1'
	.' SET todo_datetime = :datetime'
	.', todo_subject = :subject'
	.' , todo_detail = :detail'
	.' WHERE id = :id';
	echo($sql);
	$stmt = $pdo->prepare($sql);


	// Pass value to statement
	$stmt->bindValue(':datetime', strftime("%F %T", strtotime($datetime)));
	$stmt->bindValue(':subject', pg_escape_string($subject));
	$stmt->bindValue(':detail', pg_escape_string($detail));
	$stmt->bindValue(':id',(int)$id);

	// Execute UPDATE statement
	$stmt->execute();

	// Return updated the number of rows
	return $stmt->rowCount();
	
}

function getRecordById($pdo, $id){
	// Prepare and execute SELECT statement
	$id = (int)$id;
	$condition = "WHERE id = $id";
	$stmt = $pdo->query('SELECT *'
		.' FROM kyudo_tbl '
		. $condition
		);

	// Get SELECT result
	$record = array();
	while($row = $stmt->fetch(\pdo::FETCH_ASSOC)){
		$record = array(
			'id' => $row['id'],
			'datetime' => $row['kyudo_datetime'],
			'player_name' => $row['player_neme'],
			'hit_record' => $row['hit_record'],
			);
		break;
	}
	return $record;
}

function allTodo($pdo, $days=""){
	// Prepare SELECT statement
	// Condition by the number of days
	ini_set( 'display_errors', 1 );
	ini_set( 'error_reporting', E_ALL );
	$days = (int)$days;
	if($days > 0){
		$today = time();
		$todate = $today + $days*3600*24;
		// Unix time stamp to ISO
		$ftodate = strftime("'%F %T'", $todate);
		$condition = "datetime <= $ftodate";
		//echo($condition);
	}else{
		$condition = "(true)";
	}

	// Execute SELECT statement
	$stmt = $pdo->query('SELECT *'
	. ",'('|| substring(to_char(datetime, 'Day') from 1 for 3) || ')' dow "
	. ' FROM kyudo_tbl'
	. ' WHERE '.$condition
	. ' ORDER BY datetime'
	);

	//Get SELECT result
	$todos = array();
	while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
		$todos[] = array(
				'id' => $row['id'],
				'datetime' => $row['datetime'],
				'player_name' => $row['player_name'],
				'hit_record' => $row['hit_record']
				);
	}
	return $todos;
}

?>