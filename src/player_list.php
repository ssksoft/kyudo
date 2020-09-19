<?php
$competition_id = $_GET['competition_id'];

// 選手テーブルからデータ取得
try {
  $players = get_all_players($pdo, $competition_id);
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
      <th>選手ID</th>
      <th>団体名</th>
      <th>選手名</th>
      <th>称号段位</th>
      <th>順位</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($players as $player) : ?>
      <tr>
        <td class="dash-line">
          <?php
          echo htmlspecialchars($player['player_id']);
          ?>
        </td>
        <td class="dash-line">
          <?php
          echo htmlspecialchars($player['player_name']);
          ?>
        </td>
        <td>
          <?php
          echo htmlspecialchars($player['team_name']);
          ?>
        </td>
        <td>
          <?php
          echo htmlspecialchars($player['dan']);
          ?>
        </td>
        <td>
          <?php
          echo htmlspecialchars($player['rank']);
          ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>