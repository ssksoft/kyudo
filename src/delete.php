<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$id = NULL;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
}


if (isset($id)) {
    $delete_msg = "ID" . strval($id) . "の記録を削除しました。";
    try {
        delete_one_record($pdo, $id);
    } catch (\PDOException $e) {
        error_log("\PDO::Exception: " . $e->getMessage());
        echo ("error message: <br />");
        echo ($e->getMessage());
        return;
    }
} else {
    echo ("全削除");
    echo (strval($id));
    $delete_msg = "全記録を削除しました。";
    try {
        delete_all_record($pdo);
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