<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$competition_id = NULL;
if (!empty($_POST['competition_id'])) {
  $competition_id = intval($_POST['competition_id']);
  $task = "Refresh";
} else {
  $task = "Save";
  echo "hello save";
}

// View for confirmation
$competition_name = htmlspecialchars($_POST['competition_name']);
$competition_type = htmlspecialchars($_POST['competition_type']);

echo "competition_id num is";
echo $competition_id;

?>

<table>
  <tr>
    <td>
      大会名
    </td>
    <td>
      <?php
      echo $competition_name;
      ?>
    </td>
  </tr>
  <tr>
    <td>
      大会種別
    </td>
    <td>
      <?php
      echo $competition_type;
      ?>
    </td>
  </tr>
</table>

<?php
$competition_name = ($_POST['competition_name']);
$competition_type = ($_POST['competition_type']);


if (isset($competition_id)) {
  try {
    echo "hello update";
    $num = update_competition($pdo, $competition_id, $competition_name, $competition_type);
  } catch (\PDOException $e) {
    error_log("\PDO::Exception: " . $e->getMessage());
    echo (" error message: <br />");
    echo ($e->getMessage());
    return;
  }
  error_log("UPDATE: affected lins = $num");
} else {
  try {
    echo "hello insert";
    $last_competition_id = insert_competition($pdo, $competition_name, $competition_type);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  error_log("INSERT: new id = $competition_id");
}


?>
<center>
  <table borderwith='1'>
    <tr>
      <td>[<a href="/kyudo/?mode=competition_list">大会一覧</a>]</td>
      <td>[<a href="/kyudo/?mode=edit_competition&competition_id=
            <?php {
              echo $competition_id;
            } ?>">再編集</a>]</td>
    </tr>
  </table>
</center>