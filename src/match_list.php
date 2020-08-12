<?php

// テーブルからデータ取得
try {
  $matches = get_all_match($pdo);
} catch (\PDOException $e) {
  error_log("\PDO::Exception:" . $e->getMessage());
  echo ($e->getMessage());
  echo ("...UnderMaintenance");
}

//All
?>

<div class="left-column">
  <a href="/kyudo/?mode=edit_match">新しい試合を追加</a>
</div>

<link rel="stylesheet" href="table.css">
<table class="match_table" border="1">
  <thead>
    <tr>
      <th>試合ID</th>
      <th>試合名</th>
      <th>編集</th>
      <th>削除</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($matches as $match) : ?>
      <tr>
        <td>
          <?php
          echo htmlspecialchars($match['match_id']);
          ?>
        </td>
        <td>
          <?php
          echo htmlspecialchars($match['match_name']);
          ?>
        </td>
        <td>
          <a href="/kyudo/?mode=edit_match&match_id=
            <?php
            printf("%d", (int) $match['match_id']);
            ?>">
            編集
          </a>
        </td>
        <td>
          <a href="/kyudo/?mode=delete_match&match_id=
            <?php
            printf("%d", (int) $match['match_id']);
            ?>">
            削除
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>