<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require 'record_manager.php';
$id = NULL;

if (isset($_GET['id'])) {
  echo ($_GET['mode']);
  $id = intval($_GET['id']);
  // Get Result of selected id
  try {
    $record = getRecordById($pdo, $id);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  echo ("no exception");
  $title = "Edit($id)";
  $datetime = htmlspecialchars($record['datetime']);
  $player_id = htmlspecialchars($record['player_id']);
  $hit_record = htmlspecialchars($record['hit_record']);
} else {

  $title = "行射記録";
  $datetime = $now; // Default value is current time as template
  $hit_record = '';
  if (isset($_POST['player_id'])) {
    $player_id = intval($_POST['player_id']);
    $player = get_player($pdo, $player_id);
    $player_name = $player['player_name'];
  } else {
    $player_id = '';
    $player_name = '';
  }
}
$record_manager = new RecordManager();
$record_str = $record_manager->get_record_as_str($hit_record);

echo ($_GET['competition_id']);

// Form view as follows:
?>

<center>
  <font size="5"><?php echo $title; ?></font><br>
  </font>
</center>
<table>
  <tr>
    <td>
      <form action="/kyudo/?mode=save&competition_id=" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <font size=-1><tt><b>日時</b></tt></font><br />
        <input type="text" name="datetime" size="19" value="<?php echo $datetime; ?>" />
        <br />
        <table>
          <tr>
            <td>4本目</td>
            <td> <select name="hit_record4">
                <option value="○">○</option>
                <option value="×">×</option>
                <option value=<?php
                              echo mb_substr($record_str, 3, 1);
                              ?> selected>
                  <?php
                  echo mb_substr($record_str, 3, 1);
                  ?>
                </option>
            </td>
          </tr>
          <tr>
            <td>3本目</td>
            <td> <select name="hit_record3">
                <option value="○">○</option>
                <option value="×">×</option>
                <option value=<?php
                              echo mb_substr($record_str, 2, 1);
                              ?> selected>
                  <?php
                  echo mb_substr($record_str, 2, 1);
                  ?>
                </option>
            </td>
          </tr>
          <tr>
            <td>2本目</td>
            <td> <select name="hit_record2">
                <option value="○">○</option>
                <option value="×">×</option>
                <option value=<?php
                              echo mb_substr($record_str, 1, 1);
                              ?> selected>
                  <?php
                  echo mb_substr($record_str, 1, 1);
                  ?>
                </option>
            </td>
          <tr>
            <td>1本目</td>
            <td> <select name="hit_record1">
                <option value="○">○</option>
                <option value="×">×</option>
                <option value=<?php
                              echo mb_substr($record_str, 0, 1);
                              ?> selected>
                  <?php
                  echo mb_substr($record_str, 0, 1);
                  ?>
                </option>
            </td>
          </tr>
          <tr>
            <td>
              選手名
            </td>
            <td>
              <?php
              echo $player_name;
              ?>
              </a>
            </td>
          </tr>
        </table>
        <center>
          <input type="submit" name="SaveOpt" value="Cancel" />
          <input type="submit" name="SaveOpt" value="Save" />
        </center>
      </form>
      <table>
        <tr>
          <td>
            選手ID
          </td>
          <td>
            <form action="/kyudo/?mode=edit_hit_record" method="post">
              <input type="text" name="player_id" value="<?php
                                                          echo $player_id;
                                                          ?>">

              <input type="submit" value="選手名を表示する">
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>