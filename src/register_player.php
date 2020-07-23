<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// View for confirmation
$player_name = htmlspecialchars($_POST['player_name']);
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
$player_name = ($_POST['player_name']);


try {
  $id = insert_player($pdo, $player_name);
} catch (\PDOException $e) {
  echo ($e->getMessage());
  error_log("\PDO::Exception: " . $e->getMessage());
  return;
}
error_log("INSERT: new id = $id");



?>
<center>
  <table borderwith='1'>
    <tr>
      <td>[<a href="/kyudo/?mode=list">All</a>]</td>
      <td>[<a href="/kyudo/?mode=edit&id=<?php {
                                            echo $id;
                                          } ?>">Re-edit</a>]</td>
    </tr>
  </table>
</center>