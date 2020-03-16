<?php
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );
$id=NULL;

if(isset($_GET['id'])){
	echo($_GET['mode']);
	$id = intval($_GET['id']);
	// Get Result of selected id
	try{
	$record = getRecordById($pdo, $id);
	} catch (\PDOException $e){
		echo($e->getMessage());
		error_log("\PDO::Exception: " . $e->getMessage() );
		return;
	}
	echo("no exception");
	$title = "Edit($id)";
	$datetime = htmlspecialchars($record['datetime']);
	$player_name = htmlspecialchars($record['player_name']);
	$hit_record = htmlspecialchars($record['hit_record']);
}else{
	$title = "行射記録";
	$datetime = $now; // Default value is current time as template
	$player_name = '';
	$hit_record = '';
}
// Form view as follows:
?>
<center>
<font size="5"><?php echo $title;?></font><br>
<font size="4"><?php echo("get_parameter:");
echo($_GET['mode']);?></font>
</center>
<table>
<tr><td>
<form action="/kyudo/?mode=save" method="post">
	<input type="hidden" name = "id" value="<?php echo $id; ?>"/>
	<font size =-1><tt><b>日時</b></tt></font><br/>
	<input type="text" name="datetime" size="19" value="<?php echo $datetime;?>"/><br/>
	
	<font size=-1><tt><b>選手名</b></tt></font><br/>
	<input type = "text" name = "player_name" size="19" value="<?php echo $hit_record;?>"/><br/>
	
	<font size=-1><tt><b>競技記録</b></tt></font><br/>
	<input type = "text" name = "hit_record" size="19" value="<?php echo $hit_record;?>"/><br/>
	<center><input type="submit" name="SaveOpt" value="Cancel"/>
	<input type="submit" name="SaveOpt" value="Save"/></center>
</form>
</td></tr>
</table>

