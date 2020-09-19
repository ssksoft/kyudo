<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$player_name = htmlspecialchars($_POST['player_name']);
$competition_id = $_GET['competition_id'];


// View for confirmation
?>
<table borderwith='1'>
  <tr>
    <td>選手名</td>
    <td>
      <?php
      echo $player_name;
      ?>
    </td>
  </tr>
</table>

<?php

try {
  $id = insert_player($pdo, $player_name, $competition_id);
} catch (\PDOException $e) {
  echo ($e->getMessage());
  error_log("\PDO::Exception: " . $e->getMessage());
  return;
}
error_log("INSERT: new id = $id");



?>
<left>
  <table borderwith='1'>
    <tr>
      <td>[<a href="/kyudo/?mode=list">All</a>]</td>
      <td>[<a href="/kyudo/?mode=edit&id=<?php {
                                            echo $id;
                                          } ?>">Re-edit</a>]</td>
    </tr>
  </table>
</left>