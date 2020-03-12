<?php

require 'dbaccess.php';

// Initialize
try {
	// Read parameters from configuration file as .ini
	$params = parse_ini_file('conf/kyudo.ini', true);
	if ($params === false) {
		throw new \Exception("Error reading ini configuration file");
	}

	//DB connection
	$pdo = connect($params['database']);
} catch (\PDOException $e) {
	error_log("\PDO::Exception" . $e->getMessage());
	echo ($e->getMessage());
	echo ("Under maintenance");
	goto end;
}

// Remember current time
$now = strftime('%F %T', time());

// Body header as follows
?>
<div class="container">
	<h1>弓道競技記録システム</h1>
	<font size="3">
	</font>
	<div class="left-column">
		<a href="/kyudo/"> [All] </a>
		<a href="/kyudo/?mode=edit">[Edit]</a>
	</div>
	<div class="right-column"><?php echo $now; ?></div>
	<div>
		<blockquote>
			<hr size="1">

			<?php

			// Switch page contents by URL parameter

			if (!empty($_GET['mode']))
				$mode = $_GET['mode'];
			else
				$mode = '';
			//Confirm option when save
			if ($mode == "save" && $_POST['SaveOpt'] != "Save") {
				// Change list when not save
				echo "<center> canceled </center>";
				$mode = "list";
			}

			switch ($mode) {
				case 'edit':
					// Edit || Create
					include "edit.php";
					break;
				case 'save':
					// Save
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
	<img src="/icons/back.gif"><a href="/kyudo/">back</a><br />
</div>
<?php end: ?>
<div class="right-column">
	<img src="/icons/layout.gif"><a href="/kyudo/index.php">To top</a>
</div>