<?php
require 'record_manager.php';

if (isset($_POST['FORDAYS']))
    $days = (int) $_POST['FORDAYS'];
else
    $days = 30; //30daysasdefault

//GettheTodo
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
<center>
    <table class="hit_table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>選手名</th>
                <th>記録</th>
                <th>編集</th>
                <th>削除</th>
            </tr>
        </thead>
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
                        echo htmlspecialchars($kyudo['player_name']);
                        ?>
                    </td>
                    <td class="dash-line">
                        <?php
                        $record_manager = new RecordManager();
                        $record_str = $record_manager->get_record_as_str($kyudo['hit_record']);
                        echo htmlspecialchars($record_str);
                        ?>
                    </td>
                    <td class="dash-line">
                        <a href="/kyudo/?mode=edit&id=
                            <?php
                            printf("%d", (int) $kyudo['id']); ?>">編集
                        </a>
                    </td>
                    <td class="delete_one">
                        <a href="/kyudo/?mode=delete&id=
                            <?php
                            printf("%d", (int) $kyudo['id']); ?>">削除
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" class="last-line">
                </td>
            </tr>
        </tbody>
    </table>
</center>