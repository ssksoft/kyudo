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

function insert_hit_record($pdo, $player_id = '', $hit_record = '')
{
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // Prepare INSERT statement
  $sql = 'INSERT INTO kyudo_tbl'
    . '(player_id, hit_record)'
    . ' VALUES'
    . ' (:player_id, :hit_record)';
  $stmt = $pdo->prepare($sql);

  // Pass value to statement
  $stmt->bindValue(':player_id', pg_escape_string($player_id));
  $stmt->bindValue(':hit_record', pg_escape_string($hit_record));

  // Execute statement
  $stmt->execute();

  // Return numbered ID
  return $pdo->lastInsertId('kyudo_tbl_id_seq');
}

function update_hit_record($pdo, $record_id, $player_id, $hit_record)
{
  //Prepare UPDATE statement
  $sql = 'UPDATE kyudo_tbl'
    . ' SET player_id = :player_id'
    . ' , hit_record = :hit_record'
    . ' WHERE record_id = :record_id';
  $stmt = $pdo->prepare($sql);


  // Pass value to statement
  $stmt->bindValue(':player_id', pg_escape_string($player_id));
  $stmt->bindValue(':hit_record', pg_escape_string($hit_record));
  $stmt->bindValue(':record_id', (int) $record_id);

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
      'competition_type' => $row['competition_type']
    );
  }
  return $competitions;
}


function get_competition($pdo, $competition_id)
{
  $condition = "competition_id = $competition_id";
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM competition_tbl'
      . ' WHERE ' . $condition
  );

  //Get SELECT result
  $competition = array();
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  $competition = array(
    'competition_id' => $row['competition_id'],
    'competition_name' => $row['competition_name'],
    'competition_type' => $row['competition_type']
  );
  return $competition;
}

function insert_competition(
  $pdo,
  $competition_name = '',
  $competition_type = ''
) {
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // Prepare INSERT statement
  $sql = 'INSERT INTO competition_tbl'
    . ' (competition_name,competition_type)'
    . ' VALUES'
    . ' (:competition_name, :competition_type)';
  $stmt = $pdo->prepare($sql);

  // Pass value to statement
  $stmt->bindValue(':competition_name', pg_escape_string($competition_name));
  $stmt->bindValue(':competition_type', pg_escape_string($competition_type));

  // Execute statement
  $stmt->execute();

  // Return numbered ID
  return $pdo->lastInsertId('competition_tbl_competition_id_seq');
}

function update_competition(
  $pdo,
  $competition_id,
  $competition_name,
  $competition_type
) {
  //Prepare UPDATE statement
  $sql = 'UPDATE competition_tbl'
    . ' SET '
    . ' competition_name = :competition_name,'
    . ' competition_type = :competition_type'
    . ' WHERE competition_id = :competition_id';
  $stmt = $pdo->prepare($sql);


  // Pass value to statement
  $stmt->bindValue(':competition_id', (int) $competition_id);
  $stmt->bindValue(':competition_name', pg_escape_string($competition_name));
  $stmt->bindValue(':competition_type', pg_escape_string($competition_type));

  // Execute UPDATE statement
  $stmt->execute();

  // Return updated the number of rows
  return $stmt->rowCount();
}

function delete_one_competition($pdo, $competition_id)
{
  // Prepare and execute Delete statement
  $condition = " WHERE competition_id = $competition_id";
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM competition_tbl'
      . $condition
  );
  // $stmt->execute();
}

function delete_all_competition($pdo)
{
  // Prepare and execute Delete statement
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM competition_tbl'
  );
}

function get_all_match($pdo, $competition_id)
{
  if ($competition_id) {
    $condition = " WHERE competition_id = $competition_id";
  } else {
    $condition = "";
  }
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM match_tbl'
      . $condition
      . ' ORDER BY match_id'

  );

  //Get SELECT result
  $matches = array();
  while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $matches[] = array(
      'match_id' => $row['match_id'],
      'match_name' => $row['match_name'],
      'competition_id' => $row['competition_id']
    );
  }
  return $matches;
}

function get_match($pdo, $match_id)
{
  $condition = "match_id = $match_id";
  // Execute SELECT statement
  $stmt = $pdo->query(
    'SELECT *'
      . ' FROM match_tbl'
      . ' WHERE ' . $condition
  );

  //Get SELECT result
  $match = array();
  $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  $match = array(
    'match_id' => $row['match_id'],
    'match_name' => $row['match_name']
  );
  return $match;
}


function insert_match(
  $pdo,
  $competition_id,
  $match_name = ''
) {
  echo $competition_id;
  ini_set('display_errors', 1);
  ini_set('error_reporting', E_ALL);
  // Prepare INSERT statement
  $sql = 'INSERT INTO match_tbl'
    . ' (match_name,competition_id)'
    . ' VALUES'
    . ' (:match_name, :competition_id)';
  $stmt = $pdo->prepare($sql);

  // Pass value to statement
  $stmt->bindValue(':match_name', pg_escape_string($match_name));
  $stmt->bindValue(':competition_id', (int) $competition_id);


  // Execute statement
  $stmt->execute();

  // Return numbered ID
  return $pdo->lastInsertId('match_tbl_match_id_seq');
}

function update_match(
  $pdo,
  $match_id,
  $match_name
) {
  //Prepare UPDATE statement
  $sql = 'UPDATE match_tbl'
    . ' SET '
    . ' match_name = :match_name'
    . ' WHERE match_id = :match_id';
  $stmt = $pdo->prepare($sql);


  // Pass value to statement
  $stmt->bindValue(':match_id', pg_escape_string($match_id));
  $stmt->bindValue(':match_name', pg_escape_string($match_name));

  // Execute UPDATE statement
  $stmt->execute();

  // Return updated the number of rows
  return $stmt->rowCount();
}

function delete_one_match($pdo, $match_id)
{
  // Prepare and execute Delete statement
  $condition = " WHERE match_id = $match_id";
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM match_tbl'
      . $condition
  );
  // $stmt->execute();
}


function delete_all_match($pdo)
{
  // Prepare and execute Delete statement
  $stmt = $pdo->query(
    'DELETE'
      . ' FROM match_tbl'
  );
}
