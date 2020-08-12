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
  echo ("... Under maintenance");
  goto end;
}

// Remember current time
$now = strftime('%F %T', time());

// Body header as follows
?>
<div class="container">
  <h1>ユミシス（弓道競技記録システム）</h1>
  <font size="3">
  </font>
  <div class="left-column">
    <a href="/kyudo/">トップ</a>
    <a href="/kyudo/?mode=competition_list">大会を選択する</a>
    <a href="/kyudo/?mode=edit_player">選手登録</a>
    <a href="/kyudo/?mode=player_list">選手一覧</a>
    <a href="/kyudo/?mode=edit">新規記録</a>
    <a href="/kyudo/?mode=delete">全記録の削除</a>
    <a href="/kyudo/?mode=sandbox">sandbox</a>
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
      if ($mode == "save" && $_POST['save_competition'] != "Save") {
        // Change list when not save
        echo "<center> canceled </center>";
        $mode = "competition_list";
      }
      if ($mode == "save" && $_POST['save_competition'] != "Save") {
        // Change list when not save
        echo "<center> canceled </center>";
        $mode = "competition_list";
      }

      switch ($mode) {
        case 'competition_list':
          include "competition_list.php";
          break;
        case 'edit_competition':
          include "edit_competition.php";
          break;
        case 'save_competition':
          include "save_competition.php";
          break;
        case 'delete_competition':
          include "delete_competition.php";
          break;
        case 'match_list':
          include "match_list.php";
          break;
        case 'edit_match':
          include "edit_match.php";
          break;
        case 'save_match':
          include "save_match.php";
          break;
        case 'delete_match':
          include "delete_match.php";
          break;
        case 'edit_player':
          include "edit_player.php";
          break;
        case 'register_player':
          include "register_player.php";
          break;
        case 'player_list':
          include "player_list.php";
          break;
        case 'edit':
          // Edit || Create
          include "edit.php";
          break;
        case 'save':
          // Save
          include "save.php";
          break;
        case 'delete':
          include "delete.php";
          break;
        case 'sandbox':
          include "sandbox/sandbox.php";
          break;
        default:
          break;
      }

      // Futter
      ?>

    </blockquote>
  </div>
</div>
<?php end: ?>
<div class="right-column">
  <img src="/icons/layout.gif"><a href="/kyudo/index.php">トップ</a>
</div>