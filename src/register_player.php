<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$team_name = htmlspecialchars($_POST['team_name']);
$player_name = htmlspecialchars($_POST['player_name']);
$dan = htmlspecialchars($_POST['dan']);
$competition_id = $_GET['competition_id'];


// View for confirmation
?>
<table borderwith='1'>
  <tr>
    <td>団体名</td>
    <td>
      <?php
      echo $team_name;
      ?>
    </td>
  </tr>
  <tr>
    <td>選手名</td>
    <td>
      <?php
      echo $player_name;
      ?>
    </td>
  </tr>
  <tr>
    <td>称号段位</td>
    <td>
      <?php
      echo $dan;
      ?>
    </td>
  </tr>
</table>

<?php

try {
  $id = insert_player($pdo, $competition_id, $team_name, $player_name, $dan);
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