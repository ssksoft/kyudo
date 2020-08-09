<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$competition_id = NULL;

if (isset($_GET['competition_id'])) {
  echo "hello competition id";
  $competition_id = intval($_GET['competition_id']);
  // Get Result of selected id
  try {
    $competition = get_competition($pdo, $id);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  $competition_name = htmlspecialchars($competition['competition_name']);
  $competition_type = htmlspecialchars($competition['competition_type']);
} else {
  echo "hello not   competition id";
  $title = "新しい大会を追加";
  if (isset($_POST['competition_id'])) {
    $competition_name = $competition['competition_name'];
    $competition_type = $competition['competition_type'];
  } else {
    $competition_name = '';
    $competition_type = '';
  }
}

// Form view as follows:
?>
<center>
  <font size="5"><?php echo $title; ?></font><br>
  </font>
</center>
<table>
  <tr>
    <td>
      <form action="/kyudo/?mode=save_competition" method="post">
        <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>" />
        <br />
        <table>
          <tr>
            <td>
              大会名
            </td>
            <td>
              <input type="text" name="competition_name">
            </td>
          </tr>
          <tr>
            <td>
              大会種別
            </td>
            <td>
              <input type="text" name="competition_type">
            </td>
          </tr>
        </table>
        <center>
          <input type="submit" name="save_competition" value="Cancel" />
          <input type="submit" name="save_competition" value="Save" />
        </center>
    </td>
  </tr>
</table>