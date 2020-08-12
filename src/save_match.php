<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$match_id = NULL;
if (!empty($_POST['match_id'])) {
  $match_id = intval($_POST['match_id']);
  $task = "Refresh";
} else {
  $task = "Save";
}

// View for confirmation
$match_name = htmlspecialchars($_POST['match_name']);

?>

<table>
  <tr>
    <td>
      試合名
    </td>
    <td>
      <?php
      echo $match_name;
      ?>
    </td>
  </tr>
</table>

<?php
$match_name = ($_POST['match_name']);


if (isset($match_id)) {
  try {
    $num = update_match($pdo, $match_id, $match_name);
  } catch (\PDOException $e) {
    error_log("\PDO::Exception: " . $e->getMessage());
    echo (" error message: <br />");
    echo ($e->getMessage());
    return;
  }
  error_log("UPDATE: affected lins = $num");
} else {
  try {
    $last_match_id = insert_match($pdo, $match_name);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  error_log("INSERT: new id = $match_id");
}


?>
<center>
  <table borderwith='1'>
    <tr>
      <td>[<a href="/kyudo/?mode=match_list">試合一覧</a>]</td>
      <td>[<a href="/kyudo/?mode=edit_match&match_id=
            <?php {
              echo $match_id;
            } ?>">再編集</a>]</td>
    </tr>
  </table>
</center>