<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$competition_id = NULL;

if (isset($_GET['competition_id'])) {
    $competition_id = intval($_GET['competition_id']);
} else {
}


if (isset($competition_id)) {
    $delete_msg = "大会ID" . strval($competition_id) . "の記録を削除しました。";
    try {
        delete_one_competition($pdo, $competition_id);
    } catch (\PDOException $e) {
        error_log("\PDO::Exception: " . $e->getMessage());
        echo ("error message: <br />");
        echo ($e->getMessage());
        return;
    }
} else {
    $delete_msg = "全大会を削除しました。";
    try {
        delete_all_competition($pdo);
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