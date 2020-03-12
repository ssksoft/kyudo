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
//echo("id=$id");

// View for confirmation
$DateTime = htmlspecialchars($_POST['DateTime']);
$Subject = htmlspecialchars($_POST['Subject']);
$Detail = htmlspecialchars($_POST['Detail']);
echo<<<EOT
	<table borderwith='1'>
	<tr><th align="left">DateTime</th><td>$DateTime</td></tr>
	<tr><th colspan="2" align="left">Detail</th></tr>
	<tr><td></td><td>$Detail</td></tr>
	</table>
EOT;

$datetime = ($_POST['DateTime']);
$subject = ($_POST['Subject']);
$detail = ($_POST['Detail']);
if(isset($id)){
	echo("id_not_null");
	try{
		$num = updateTodo($pdo, $id, $datetime, $subject, $detail);
	}catch (\PDOException $e){
	error_log( "\PDO::Exception: " . $e->getMessage());
	echo("error message: <br />");	
	echo($e->getMessage());
	return;
	}
	error_log("UPDATE: affected lins = $num");
}else{
	//echo("id_null");
	try{
		//echo("insert now");
		$id = insertTodo($pdo, $datetime, $subject, $detail);
		
}catch(\PDOException $e){
	//echo($e->getMessage());
	error_log( "\PDO::Exception: " . $e->getMessage() );
	return;
}
	error_log("INSERT: new id = $id");
}

?>
<center>
<table borderwith='1'>
	<tr>
	<td>[<a href="/todo/?mode=list">All</a>]</td>
	<td>[<a href="/todo/?mode=edit&id=<?php {echo $id;}?>">Re-edit</a>]</td>
	</tr>
</table>
</center>
