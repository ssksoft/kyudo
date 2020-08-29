<?php
// テーブルからデータ取得
try {
  $competitions = get_all_competition($pdo);
} catch (\PDOException $e) {
  error_log("\PDO::Exception:" . $e->getMessage());
  echo ($e->getMessage());
  echo ("...UnderMaintenance");
}

//All
?>

<div class="left-column">
  <a href="/kyudo/?mode=edit_competition">新しい大会を追加</a>
</div>

<link rel="stylesheet" href="table.css">
<table class="competition_table" border="1">
  <thead>
    <tr>
      <th>大会ID</th>
      <th>大会名</th>
      <th>編集</th>
      <th>削除</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($competitions as $competition) : ?>
      <tr>
        <td>
          <?php
          echo htmlspecialchars($competition['competition_id']);
          ?>
        </td>
        <td>
          <a href="/kyudo/?mode=match_list&competition_id=
            <?php
            printf("%d", (int) $competition['competition_id']);
            ?>">
            <?php
            echo htmlspecialchars($competition['competition_name']);
            ?>
        </td>
        <td>
          <a href="/kyudo/?mode=edit_competition&competition_id=
            <?php
            printf("%d", (int) $competition['competition_id']);
            ?>">
            編集
          </a>
        </td>
        <td>
          <a href="/kyudo/?mode=delete_competition&competition_id=
            <?php
            printf("%d", (int) $competition['competition_id']);
            ?>">
            削除
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php
switch ($mode) {
  case 'edit_player':
    include "edit_player.php";
    break;
  default:
    break;
}


?>