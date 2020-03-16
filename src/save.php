<?php
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );
$id=NULL;
if(! empty($_POST['id'])){
	$id = intval($_POST['id']);
	$task = "Refresh";
}else{
	$task = "Save";
}

// View for confirmation
$datetime = htmlspecialchars($_POST['datetime']);
$player_name = htmlspecialchars($_POST['player_name']);
$hit_record = htmlspecialchars($_POST['hit_record']);
echo<<<EOT
	<table borderwith='1'>
	<tr><th align="left">記録日</th><td>$datetime</td></tr>
	<tr><th colspan="2" align="left">選手名</th></tr>
	<tr><td></td><td>$player_name</td></tr>
	<tr><th colspan="2" align="left">的中</th></tr>
	<tr><td></td><td>$hit_record</td></tr>
	</table>
EOT;

$datetime = ($_POST['datetime']);
$player_name = ($_POST['player_name']);
$hit_record = ($_POST['hit_record']);
if(isset($id)){
	echo("id_not_null");
	try{
		$num = updateKyudo($pdo, $id, $datetime, $player_name, $hit_record);
	}catch (\PDOException $e){
	error_log( "\PDO::Exception: " . $e->getMessage());
	echo("error message: <br />");	
	echo($e->getMessage());
	return;
	}
	error_log("UPDATE: affected lins = $num");
}else{
	try{
		$id = insertKyudo($pdo, $datetime, $player_name, $hit_record);
		
}catch(\PDOException $e){
	error_log( "\PDO::Exception: " . $e->getMessage() );
	return;
}
	error_log("INSERT: new id = $id");
}

?>
<center>
<table borderwith='1'>
	<tr>
	<td>[<a href="/kyudo/?mode=list">All</a>]</td>
	<td>[<a href="/kyudo/?mode=edit&id=<?php {echo $id;}?>">Re-edit</a>]</td>
	</tr>
</table>
</center>
