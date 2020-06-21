<div class="delete_all">
    <h1>全記録を削除しました。</h1>
</div>

<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
if (isset($id)) {
} else {
    try {
        $num = delete_all_record($pdo);
    } catch (\PDOException $e) {
        error_log("\PDO::Exception: " . $e->getMessage());
        echo ("error message: <br />");
        echo ($e->getMessage());
        return;
    }
}
?>