<?php

require 'dbaccess.php';

// Initialize
try{
	// Read parameters from configuration file as .ini
	$params = parse_ini_file('conf/todo.ini', true);
	if($params === false){
		throw new \Exception("Error reading ini configuration file");
	}

	//DB connection
	$pdo = connect($params['database']);
}


catch (\PDOException $e){
	error_log("\PDO::Exception" . $e->getMessage());
	echo("test");
	echo($e->getMessage());
	echo("Under maintenance");
	goto end;
}

// Remember current time
$now = strftime('%F %T', time());

// Body header as follows
?>
	<div class="container">
	<h1>ToDo</h1>
	<font size="3">
	</font>
	<div class="left-column">
	<a href="/todo/"> [All] </a>
	<a href="/todo/?mode=edit">[Make]</a>
	</div>
	<div class="right-column"><?php echo $now;?></div>
		<div>
		<blockquote>
		<hr size="1">

<?php

// Switch page contents by URL parameter

if(!empty($_GET['mode']))
	$mode = $_GET['mode'];
else
	$mode = '';
//Confirm option when save
if($mode == "save" && $_POST['SaveOpt'] != "Save"){
	// Change list when not save
	echo "<center> canceled </center>";
	$mode = "list";
}

switch($mode){
case 'edit':
	// Edit || Create
	include "edit.php";
	break;
case 'save':
	// Save
	//echo("1234");
	include "save.php";
	break;
default:
	// All
	include "list.php";
	break;
}

// Futter
?>

	</blockquote>
	</div>
	</div>
	<div class="left-column">
	<img src="/icons/back.gif"><a href="/todo/">back</a><br/>
	</div>
<?php end: ?>
	<div class="right-column">
	<img src="/icons/layout.gif"><a href="/todo/index.php">To top</a>
	</div>
