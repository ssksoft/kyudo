<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$match_id = NULL;

if (isset($_GET['competition_id'])) {
  $competition_id = intval($_GET['competition_id']);
} else {
}

echo $competition_id;


if (isset($_GET['match_id'])) {
  $match_id = intval($_GET['match_id']);
  // Get Result of selected id
  try {
    $match = get_match($pdo, $match_id);
  } catch (\PDOException $e) {
    echo ($e->getMessage());
    error_log("\PDO::Exception: " . $e->getMessage());
    return;
  }
  $title = "Edit($match_id)";
  $match_name = htmlspecialchars($match['match_name']);
} else {
  $title = "新しい大会を追加";
  if (isset($_POST['match_id'])) {
    $match_name = $match['match_name'];
    $match_type = $match['match_type'];
  } else {
    $match_name = '';
    $match_type = '';
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
      <form action="/kyudo/?mode=save_match" method="post">
        <input type="hidden" name="match_id" value="<?php echo $match_id; ?>" />
        <br />
        <table>
          <tr>
            <td>
              試合名
            </td>
            <td>
              <input type="text" name="match_name" value=<?php
                                                          echo $match_name;
                                                          ?>>
            </td>
        </table>
        <center>
          <input type="submit" name="save_match" value="Cancel" />
          <input type="submit" name="save_match" value="Save" />
        </center>
    </td>
  </tr>
</table>