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
<center>
    <form action="/kyufo/?mode=list" method="post">
        <fontsize="5">All</font>
            <inputtype="text" size=4 maxlength=4 name="FORDAYS" value="
            <?php
            echo $days;
            ?>
            ">
                days(0=All record)
    </form>
    <?php echo ("Viewfor $days days") ?>

    <table class="table-bordered">
        <thead>
            <tr>
                <thwidth="20"class="start-line">ID</th>
                    <thwidth="80"class="start-line">選手名</th>
                        <thclass="start-line">記録</th>
                            <thwidth="40"class="start-line">**</th>
            </tr>
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