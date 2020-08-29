<?php

// 大会IDを取得
if (isset($_GET['competition_id'])) {
  $competition_id = intval($_GET['competition_id']);
} else {
}

// テーブルからデータ取得
try {
  $matches = get_all_match($pdo, $competition_id);
} catch (\PDOException $e) {
  error_log("\PDO::Exception:" . $e->getMessage());
  echo ($e->getMessage());
  echo ("...UnderMaintenance");
}

//All
?>

<div class="left-column">
  <a href="
  /kyudo/?mode=edit_match&competition_id= 
  <?php
  echo $competition_id;
  ?>
  ">
    新しい試合を追加
  </a>
</div>




<link rel=" stylesheet" href="table.css">
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
          <a href="/kyudo/?mode=edit_hit_record&match_id=
            <?php
            printf("%d", (int) $match['match_id']);
            ?>">
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