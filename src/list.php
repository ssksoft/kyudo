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
	$kyudos = allKyudo($pdo, $days);
}catch(\PDOException $e){
	error_log("\PDO::Exception:".$e->getMessage());
	echo($e->getMessage());
	echo("...Under Maintenance");
}

// All
?>
<center>
<form action="/kyufo/?mode=list" method="post">
<font size="5">All</font>
	<input type="text" size=4 maxlength=4 name="FORDAYS" value="<?php echo $days;?>">days (0=All record)
</form>
<?php echo("View for $days days")?>

<table class="table-bordered">
	<thead>
	<tr>
	<th width="20" class="start-line">ID</th>
	<th width="80" class="start-line">選手名</th>
	<th class="start-line">記録</th>
	<th width="40" class="start-line">**</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($kyudos as $kyudo) : ?>
	<tr>
	<td class="dash-line"><?php echo htmlspecialchars($kyudo['id']);?></td>
	<td class="dash-line"><?php echo htmlspecialchars($kyudo['player_name']);?></td>
	<td class="dash-line"><?php echo htmlspecialchars($kyudo['hit_record']);?></td>
		<td class="dash-line"><a href="/kyudo/?mode=edit&id=<?php printf("%d", (int)$kyudo['id']);?>">Edit</a></td>
	</tr>
	<?php endforeach;?>
	<tr>   <td colspan="5" class="last-line"></td>  </tr>
	</tbody>
</table>
</center>

