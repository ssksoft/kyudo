<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require 'record_manager.php';
$player_id[] = array();

const NUM_PLAYER = 6;

$competition_id = $_GET['competition_id'];
$competition = get_competition($pdo, $competition_id);

$match_id = $_GET['match_id'];
$match = get_match($pdo, $match_id);


// 選手情報を取得
$kyudo_tbl = array();

// 選手名を表示するボタンを押下した場合
if (isset($_POST['player_id'])) {
  $player_id = $_POST['player_id'];

  for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
    $players[$current_player] = get_player($pdo, $player_id[$current_player]);

    // kyudo_tblのデフォルト値を格納する
    $kyudo_tbl[$current_player]['competition_id'] = $competition_id; // デフォルト表示の×
    $kyudo_tbl[$current_player]['match_id'] = $match_id; // デフォルト表示の×
    $kyudo_tbl[$current_player]['player_id'] = $player_id[$current_player];

    if ($current_player < 3) {
      $kyudo_tbl[$current_player]['range'] = 2;
    } else {
      $kyudo_tbl[$current_player]['range'] = 1;
    }

    $kyudo_tbl[$current_player]['shoot_order'] = 3 - $current_player % 3;
    $kyudo_tbl[$current_player]['hit_record'] = 0; // デフォルト表示の×

  }
}
// デフォルト表示（DBから取得）
else {
  try {
    $kyudo_tbl = get_record_by_competition_id($pdo, $competition_id);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
    $players[$current_player] = get_player($pdo, $kyudo_tbl[$current_player]['player_id']);
  }
}


$title = "行射記録";
$record_manager = new RecordManager();



?>

<a href="/kyudo/?mode=competition_list">
  大会一覧
</a>
<nobr>></nobr>
<a href="/kyudo/?mode=match_list&competition_id=
<?php
echo $competition['competition_id'];
?>
">
  <?php
  echo $competition['competition_name'];
  ?>
</a>
<nobr>></nobr>
<a href="/kyudo/?mode=edit_hit_record&match_id=
<?php
echo $match['match_id'];
?>&competition_id=
<?php
echo $competition['competition_id'];
?>
">
  <?php
  echo $match['match_name'];
  ?>
</a>

<br />

<left>
  <font size="5"><?php echo $title; ?></font><br>
  </font>
</left>
<table>
  <tr>
    <td>
      <form action="/kyudo/?mode=save" method="post">
        <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>" />
        <?php
        for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
        ?>
          <input type="hidden" name="player_id[]" value="<?php echo $kyudo_tbl[$current_player]['player_id']; ?>" />
        <?php
        }
        ?>

        <input type="hidden" name="match_id" value="<?php echo $match_id; ?>" />

        <br />
        <table border="1">
          <?php
          for ($current_shot = 3; $current_shot >= 0; $current_shot--) {
          ?>
            <tr>
              <td>
                <?php
                echo $current_shot + 1
                ?>
                本目</td>
              <?php
              for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
                $record_num = $kyudo_tbl[$current_player]['hit_record'];
                $record_str = $record_manager->get_record_as_str($record_num);
              ?>
                <td> <select name="hit_record[]">
                    <option value="○">○</option>
                    <option value="×">×</option>
                    <option value=<?php
                                  echo mb_substr($record_str, $current_shot, 1);
                                  ?> selected>
                      <?php
                      echo mb_substr($record_str, $current_shot, 1);
                      ?>
                    </option>
                </td>
              <?php
              }
              ?>
            </tr>
          <?php
          }
          ?>
          <tr>
            <td>
              選手名
            </td>

            <?php
            for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
              echo "<td>";
              echo $players[$current_player]['player_name'];
              echo "</td>";
            }
            ?>

          </tr>
          <tr>
            <td>
              立順
            </td>
            <?php
            for ($current_team = 0; $current_team < 2; $current_team++) {
              for ($current_order = 3; $current_order > 0; $current_order--) {
                echo "<td>";
                echo $current_order;
            ?>
                <input type="hidden" name="shoot_order[]" value="<?php echo $current_order; ?>" />
            <?php
                echo "</td>";
              }
            }
            ?>

          </tr>
          <tr>
            <td>
              団体名
            </td>
            <?php
            if (($players[0]['team_name'] == $players[1]['team_name'] && $players[1]['team_name'] == $players[2]['team_name'] && $players[0]['team_name'] == $players[2]['team_name']) && ($players[3]['team_name'] == $players[4]['team_name'] && $players[4]['team_name'] == $players[5]['team_name'] && $players[3]['team_name'] == $players[5]['team_name'])) {

              echo "<td colspan=\"3\">";
              echo $players[0]['team_name'];
              echo "</td>";
              echo "<td colspan=\"3\">";
              echo $players[3]['team_name'];
              echo "</td>";
            } elseif ($team_name[0] == $team_name[1] && $team_name[1] == $team_name[2] && $team_name[0] == $team_name[2]) {

              for ($current_team = 0; $current_team < NUM_PLAYER / 3; $current_team++) {
                echo '<td colspan="3">';
                echo $team_name[0];
                echo "</td>";
              }
              for ($current_team = 0; $current_team < NUM_PLAYER / 3; $current_team++) {
                echo '<td>';
                echo $team_name[2];
                echo "</td>";
              }
            } elseif (($team_name[3] == $team_name[4] && $team_name[4] == $team_name[5] && $team_name[3] == $team_name[5])) {
              for ($current_team = 0; $current_team < NUM_PLAYER / 3; $current_team++) {
                echo '<td>';
                echo $team_name[0];
                echo "</td>";
              }
              echo '<td colspan="3">';
              echo $team_name[3];
              echo "</td>";
            }
            ?>
          </tr>
          <tr>
            <td>
            </td>
            <?php
            for ($current_range = 2; $current_range > 0; $current_range--) {

              echo '<td colspan="3">';
              echo '第';
              echo $current_range;
              echo '射場';
            ?>
              <input type="hidden" name="range[]" value="<?php echo $current_range; ?>" />
            <?php
              echo "</td>";
            }
            ?>

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

          <form action="/kyudo/?mode=edit_hit_record&match_id=
              <?php
              echo $match['match_id'];
              ?>
              &competition_id=
              <?php
              echo $competition['competition_id'];
              ?>
              " method="post">
            <?php
            for ($current_player = 0; $current_player < NUM_PLAYER; $current_player++) {
            ?>
              <td>
                <input type="text" name="player_id[]" value="<?php echo $kyudo_tbl[$current_player]['player_id']; ?>">
              </td>
            <?php
            }
            ?>
            <td>
              <input type="submit" value="選手名を表示する">
            </td>
          </form>
    </td>
  </tr>
</table>
</td>
</tr>
</table>