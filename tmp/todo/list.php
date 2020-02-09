<?php
/*
 * Read schedule from ToDo DB and show all
 */

// The number of display days and overrie argument if there is parameter in array
if(isset($_POST['FORDAYS']))
	$days = (int)$_POST['FORDAYS'];
else
	$days = 30; // 30 days as default

// Get the Todo
try{
	//$todos = allTodo($pdo, $days, 'datetime');
	$todos = allTodo($pdo, $days);
}catch(\PDOException $e){
	error_log("\PDO::Exception:".$e->getMessage());
	echo($e->getMessage());
	echo("Under Maintenance1234");
}

// All
?>
<center>
<form action="/todo/?mode=list" method="post">
<font size="5">All</font>
	<input type="text" size=4 maxlength=4 name="FORDAYS" value="<?php echo $days;?>">days (0=All record)
</form>
<?php echo("View for $days days")?>

<table class="table-bordered">
	<thead>
	<tr>
	<th width="20" class="start-line">ID</th>
	<th width="180" class="start-line">Day</th>
	<th width="20" class="start-line">Date</th>
	<th class="start-line">Subject</th>
	<th width="40" class="start-line">**</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($todos as $todo) : ?>
	<tr>
	<td class="dash-line"><?php echo htmlspecialchars($todo['id']);?></td>
	<td class="dash-line"><?php echo htmlspecialchars($todo['datetime']);?></td>
	<td class="dash-line"><?php echo htmlspecialchars($todo['subject']);?></td>
		<td class="dash-line"><a href="/todo/?mode=edit&id=<?php printf("%d", (int)$todo['id']);?>">Edit</a></td>
	</tr>
	<?php endforeach;?>
	<tr>   <td colspan="5" class="last-line"></td>  </tr>
	</tbody>
</table>
</center>

