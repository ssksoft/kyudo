<?php
require 'RecordManager.php';

if (isset($_POST['FORDAYS']))
  $days = (int) $_POST['FORDAYS'];
else
  $days = 30; //30daysasdefault

// マスターテーブルからデータ取得
try {
  $kyudos = allKyudo($pdo, $days);
} catch (\PDOException $e) {
  error_log("\PDO::Exception:" . $e->getMessage());
  echo ($e->getMessage());
  echo ("...UnderMaintenance");
}

//All
?>
<link rel="stylesheet" href="table.css">
<table class="hit_table" border="1">
  <thead>
    <tr>
      <th>記録ID</th>
      <th>選手名</th>
      <th>①</th>
      <th>②</th>
      <th>③</th>
      <th>④</th>
      <th>編集</th>
      <th>削除</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($kyudos as $kyudo) : ?>
      <tr>
        <td class="dash-line">
          <?php
          echo htmlspecialchars($kyudo['id']);
          ?>
        </td>
        <td class="dash-line">
          <?php
          $player = get_player($pdo, $kyudo['player_id']);
          echo htmlspecialchars($player['player_name']);
          ?>
        </td>
        <?php
        $record_manager = new RecordManager();
        $record_str = $record_manager->get_record_as_str($kyudo['hit_record']);

        for ($i = 0; $i < mb_strlen($record_str); $i++) {
        ?>
          <td class="dash-line">
            <?php
            echo mb_substr($record_str, $i, 1);
            ?>
          </td>
        <?php
        }
        ?>
        <td class="dash-line">
          <a href="/kyudo/?mode=edit&id=
            <?php
            printf("%d", (int) $kyudo['id']);
            ?>">
            編集
          </a>
        </td>
        <td class="delete_one">
          <a href="/kyudo/?mode=delete&id=
            <?php
            printf("%d", (int) $kyudo['id']);
            ?>">
            削除
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>