<?php
function connect($params)
{
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // check parameter

  if (!isset($params['host'])) $params['host'] = '';
  if (!isset($params['port'])) $params['port'] = '';
  if (!isset($params['database'])) $params['database'] = '';
  if (!isset($params['user'])) $params['user'] = '';
  if (!isset($params['password'])) $params['password'] = '';

  // connetc with postgresql DB
  $conStr = sprintf(
    "pgsql: dbname=%s host=%s port=%d",
    $params['database'],
    $params['host'],
    $params['port']
  );

  $pdo = new PDO($conStr, $params['user'], $params['password']);
  $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  $pdo->query("SET client_encoding TO 'UTF8'");

  return $pdo;
}

function insertKyudo($pdo, $datetime, $player_id = '', $hit_record = '')
{
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // Prepare INSERT statement
  $sql = 'INSERT INTO kyudo_tbl'
    . '(datetime, player_id, hit_record)'
    . ' VALUES'
    . ' (:datetime, :player_id, :hit_record)';
  $stmt = $pdo->prepare($sql);

  // Pass value to statement
  $stmt->bindValue(':datetime', strftime("%F %T", strtotime($datetime)));
  $stmt->bindValue(':player_id', pg_escape_string($player_id));
  $stmt->bindValue(':hit_record', pg_escape_string($hit_record));

  // Execute statement
  $stmt->execute();

  // Return numbered ID
  return $pdo->lastInsertId('kyudo_tbl_id_seq');
}

function updateKyudo($pdo, $id, $datetime, $player_id, $hit_record)
{
  //Prepare UPDATE statement
  $sql = 'UPDATE kyudo_tbl'
    . ' SET datetime = :datetime'
    . ', player_id = :player_id'
    . ' , hit_record = :hit_record'
    . ' WHERE id = :id';
  $stmt = $pdo->prepare($sql);


  // Pass value to statement
  $stmt->bindValue(':datetime', strftime("%F %T", strtotime($datetime)));
  $stmt->bindValue(':player_id', pg_escape_string($player_id));
  $stmt->bindValue(':hit_record', pg_escape_string($hit_record));
  $stmt->bindValue(':id', (int) $id);

  // Execute UPDATE statement
  $stmt->execute();

  // Return updated the number of rows
  return $stmt->rowCount();
}

function getRecordById($pdo, $id)
{
  // Prepare and execute SELECT statement
  $id = (int) $id;
  $condition = "WHERE id = $id";
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM kyudo_tbl '
      . $condition
  );

  // Get SELECT result
  $record = array();
  while ($row = $stmt->fetch(\pdo::FETCH_ASSOC)) {
    $record = array(
      'id' => $row['id'],
      'datetime' => $row['datetime'],
      'player_name' => $row['player_name'],
      'hit_record' => $row['hit_record'],
    );
    break;
  }
  return $record;
}

function allKyudo($pdo, $days = "")
{
  // Prepare SELECT statement
  // Condition by the number of days
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  $days = (int) $days;
  if ($days > 0) {
    $today = time();
    $todate = $today + $days * 3600 * 24;

    // Unix time stamp to ISO
    $ftodate = strftime("'%F %T'", $todate);
    $condition = "datetime <= $ftodate";
  } else {
    $condition = "(true)";
  }

  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ",'('|| substring(to_char(datetime, 'Day') from 1 for 3) || ')' dow "
      . ' FROM kyudo_tbl'
      . ' WHERE ' . $condition
      . ' ORDER BY datetime'
  );

  //Get SELECT result
  $kyudos = array();
  while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $kyudos[] = array(
      'id' => $row['id'],
      'datetime' => $row['datetime'],
      'hit_record' => $row['hit_record'],
      'player_id' => $row['player_id']
    );
  }
  return $kyudos;
}

function delete_all_record($pdo)
{
  // Prepare and execute Delete statement
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM kyudo_tbl'
  );
}

function delete_one_record($pdo, $id)
{
  // Prepare and execute Delete statement
  $condition = " WHERE id = $id";
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM kyudo_tbl'
      . $condition
  );
}

function get_record($pdo, $id)
{
  // Prepare SELECT statement
  // Condition by the number of days
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  $condition = "id == $id";

  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM kyudo_tbl'
      . ' WHERE ' . $condition
  );

  //Get SELECT result
  $record = array();
  while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $record[] = array(
      'id' => $row['id'],
      'datetime' => $row['datetime'],
      'player_name' => $row['player_name'],
      'hit_record' => $row['hit_record']
    );
  }
  return $record;
}

function insert_player($pdo, $player_name = '')
{
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // Prepare INSERT statement
  $sql = 'INSERT INTO player_tbl'
    . '(player_name)'
    . ' VALUES'
    . ' (:player_name)';
  $stmt = $pdo->prepare($sql);

  // Pass value to statement
  $stmt->bindValue(':player_name', pg_escape_string($player_name));

  // Execute statement
  $stmt->execute();

  // Return numbered ID
  return $pdo->lastInsertId('player_tbl_player_id_seq');
}


function get_player($pdo, $player_id)
{
  $condition = "player_id = $player_id";
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM player_tbl'
      . ' WHERE ' . $condition
  );

  //Get SELECT result
  $player = array();
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  $player = array(
    'player_id' => $row['player_id'],
    'player_name' => $row['player_name'],
    'team_name' => $row['team_name'],
    'dan' => $row['dan'],
    'rank' => $row['rank']
  );
  return $player;
}

function get_all_players($pdo)
{
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM player_tbl'
      . ' ORDER BY player_id'
  );

  //Get SELECT result
  $players = array();
  while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $players[] = array(
      'player_id' => $row['player_id'],
      'player_name' => $row['player_name'],
      'team_name' => $row['team_name'],
      'dan' => $row['dan'],
      'rank' => $row['rank']
    );
  }
  return $players;
}

function get_all_competition($pdo)
{
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM competition_tbl'
      . ' ORDER BY competition_id'
  );

  //Get SELECT result
  $competitions = array();
  while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $competitions[] = array(
      'competition_id' => $row['competition_id'],
      'competition_name' => $row['competition_name'],
      'type' => $row['type']
    );
  }
  return $competitions;
}
