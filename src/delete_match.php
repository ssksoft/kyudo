<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$match_id = NULL;

if (isset($_GET['match_id'])) {
    $match_id = intval($_GET['match_id']);
} else {
}


if (isset($match_id)) {
    $delete_msg = "試合ID" . strval($match_id) . "の記録を削除しました。";
    try {
        delete_one_match($pdo, $match_id);
    } catch (\PDOException $e) {
        error_log("\PDO::Exception: " . $e->getMessage());
        echo ("error message: <br />");
        echo ($e->getMessage());
        return;
    }
} else {
    $delete_msg = "全大会を削除しました。";
    try {
        delete_all_match($pdo);
    } catch (\PDOException $e) {
        error_log("\PDO::Exception: " . $e->getMessage());
        echo ("error message: <br />");
        echo ($e->getMessage());
        return;
    }
}
?>

<div class="delete_msg">
    <h1><?php echo $delete_msg; ?></h1>
</div>