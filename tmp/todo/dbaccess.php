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

	//$dsn = 'pgsql: dbname=tododb host=localhost port=5432';
	//$user = 'todo';
	//$password = '2bChanged%';
	$pdo = new PDO($conStr, $params['user'],$params['password']);
	//$pdo = new PDO($dsn, $user, $password);
	$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	$pdo->query("SET client_encoding TO 'UTF8'");

	return $pdo;
}

function insertTodo($pdo, $datetime, $subject='', $detail=''){
	ini_set( 'display_errors', 1 );
	ini_set( 'error_reporting', E_ALL );
	// Prepare INSERT statement
	//echo("datetime=$datetime, subject=$subject,detail=$detail");
	$sql = 'INSERT INTO todo_tbl1'
	.'(todo_datetime, todo_subject, todo_detail)'
	.' VALUES'
	.' (:datetime, :subject, :detail)';
	//var_dump($pdo);
	//echo("sql_command:$sql");
	$stmt = $pdo->prepare($sql);

	// Pass value to statement
	$stmt->bindValue(':datetime', strftime("%F %T", strtotime($datetime)));
	$stmt->bindValue(':subject', pg_escape_string($subject));
	$stmt->bindValue(':detail', pg_escape_string($detail));
	
	// Execute statement
	$stmt->execute();

	// Return numbered ID
	return $pdo->lastInsertId('todo_tbl1_id_seq');
}

function updateTodo($pdo, $id, $datetime, $subject, $detail){
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

function getTodoById($pdo, $id){
	// Prepare and execute SELECT statement
	$id = (int)$id;
	$condition = "WHERE id = $id";
	$stmt = $pdo->query('SELECT *'
		.' FROM todo_tbl1 '
		. $condition
		);

	// Get SELECT result
	$todos = array();
	while($row = $stmt->fetch(\pdo::FETCH_ASSOC)){
		$todo = array(
			'id' => $row['id'],
			'datetime' => $row['todo_datetime'],
			'subject' => $row['todo_detail'],
			'detail' => $row['todo_detail'],
			);
		break;
	}
	return $todo;
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
		$condition = "todo_datetime <= $ftodate";
		//echo($condition);
	}else{
		$condition = "(true)";
	}

	// Execute SELECT statement
	$stmt = $pdo->query('SELECT *'
	. ",'('|| substring(to_char(todo_datetime, 'Day') from 1 for 3) || ')' dow "
	. ' FROM todo_tbl1'
	. ' WHERE '.$condition
	. ' ORDER BY todo_datetime'
	);

	//Get SELECT result
	$todos = array();
	while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
		$todos[] = array(
				'id' => $row['id'],
				'datetime' => $row['todo_datetime'],
				'subject' => $row['todo_subject'],
				'detail' => $row['todo_detail'],
				'dow' => $row['dow'],	
				);
	}
	return $todos;
}

?>